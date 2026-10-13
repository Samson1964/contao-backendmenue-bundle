<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\FilesModel;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\UserModel;
use Schachbulle\BackendMenueBundle\Service\AreaAccess;
use Schachbulle\BackendMenueBundle\Service\IconProvider;

/**
 * Stellt alle DCA-Callbacks des Bundles als Symfony-Service bereit.
 *
 * Ersetzt die früheren Inline-Klassen in den DCA-Dateien: Die Registrierung
 * läuft über das #[AsCallback]-Attribut, das in Contao 4.13 und 5.x unter
 * demselben Namespace verfügbar ist.
 */
class DcaCallbackListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param IconProvider    $iconProvider Der Service für die Icon-Listen
     * @param ContaoFramework $framework    Zugriff auf Contao-Models (FilesModel)
     * @param AreaAccess      $access       Entscheidet, welche Bereiche der
     *                                      angemeldete Benutzer verwalten darf
     */
    public function __construct(
        private readonly IconProvider $iconProvider,
        private readonly ContaoFramework $framework,
        private readonly AreaAccess $access,
    ) {
    }

    /**
     * Beschränkt die Bereichs-Verwaltung auf die eigenen Bereiche.
     *
     * Benutzer ohne Administratorrechte dürfen nur ihre persönlichen Bereiche
     * sehen und bearbeiten. Dafür wird dreifach abgesichert:
     *
     * 1. Die Liste wird über den Standard-Filter von DC_Table eingeschränkt.
     * 2. Die Geltungs-Legende verschwindet aus der Palette, damit niemand
     *    seinen Bereich zum globalen machen oder anderen zuschieben kann.
     * 3. Jede ID in der Anfrage wird geprüft — ohne diesen Schritt ließe sich
     *    ein fremder Bereich durch Abwandeln der Adresse bearbeiten, denn der
     *    Listen-Filter wirkt nur auf die Anzeige.
     *
     * @return void
     *
     * @throws AccessDeniedException Wenn die Anfrage einen fremden Bereich betrifft
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'config.onload')]
    public function restrictToOwnAreas(): void
    {
        if ($this->access->isAdmin()) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_backendmenue_bereiche'];
        $allowed = $this->access->getEditableAreaIds();

        // Geltung ist für Nicht-Administratoren nicht verhandelbar
        $dca['palettes']['default'] = str_replace(';{scope_legend},scope', '', $dca['palettes']['default']);

        // Der Filter wird als Zeichenkette hinterlegt, nicht mit Platzhaltern:
        // DC_Table bindet je Filter nur einen einzigen Wert ($filter[1]), eine
        // IN-Liste mit mehreren Platzhaltern führt deshalb zu einem
        // Datenbankfehler. Die IDs stammen aus der eigenen Abfrage und werden
        // zur Sicherheit in Ganzzahlen gewandelt.
        $ids = implode(',', array_map('intval', $allowed));

        $dca['list']['sorting']['filter'][] = '' === $ids ? 'id = 0' : 'id IN (' . $ids . ')';

        foreach ($this->getRequestedIds() as $id) {
            if (!$this->access->canEditArea($id)) {
                throw new AccessDeniedException('Kein Zugriff auf den Menübereich ID ' . $id . '.');
            }
        }
    }

    /**
     * Macht den anlegenden Benutzer zum Besitzer eines neuen Bereichs.
     *
     * Legt ein Benutzer ohne Administratorrechte einen Bereich an, wird er
     * automatisch als persönlicher Bereich dieses Benutzers eingetragen — das
     * Feld dafür ist in seiner Palette gar nicht sichtbar. Ohne diesen Schritt
     * entstünde ein globaler Bereich (der Vorgabewert der Spalte), den der
     * Benutzer anschließend nicht einmal mehr bearbeiten dürfte.
     *
     * @param string $table Die Tabelle, in der angelegt wird
     * @param int    $id    Die ID des neuen Datensatzes
     * @param array  $set   Die eingefügten Werte
     * @param mixed  $dc    Der Data Container
     *
     * @return void
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'config.oncreate')]
    public function assignOwnerOnCreate(string $table, int $id, array $set, $dc = null): void
    {
        if ($this->access->isAdmin()) {
            return;
        }

        $userId = $this->access->getUserId();

        if (0 === $userId) {
            return;
        }

        Database::getInstance()
            ->prepare('UPDATE tl_backendmenue_bereiche SET scope = ?, users = ? WHERE id = ?')
            ->execute('user', serialize([$userId]), $id);
    }

    /**
     * Beschränkt die Modul-Zuordnungen auf Bereiche, die dem Benutzer gehören.
     *
     * Die Kindtabelle wird über die Adresse erreicht, weshalb hier dieselbe
     * Prüfung nötig ist wie bei den Bereichen selbst: Sowohl die Eltern-ID in
     * der Adresse als auch die betroffenen Zuordnungen werden auf den Besitzer
     * ihres Bereichs geprüft.
     *
     * @return void
     *
     * @throws AccessDeniedException Wenn die Anfrage einen fremden Bereich betrifft
     */
    #[AsCallback(table: 'tl_backendmenue_zuordnungen', target: 'config.onload')]
    public function restrictAssignmentsToOwnAreas(): void
    {
        if ($this->access->isAdmin()) {
            return;
        }

        foreach ($this->getRequestedAreaIdsForAssignments() as $areaId) {
            if (!$this->access->canEditArea($areaId)) {
                throw new AccessDeniedException('Kein Zugriff auf die Modul-Zuordnungen des Bereichs ID ' . $areaId . '.');
            }
        }
    }

    /**
     * Zeigt in der Bereichs-Übersicht einen Bedienhinweis an.
     *
     * Ein Bereich erscheint erst im Backend-Menü, wenn ihm mindestens ein
     * Modul zugeordnet wurde — das ist gewolltes Verhalten (Contao blendet
     * leere Gruppen aus), aber ohne Hinweis ein Stolperstein. Die Meldung
     * erscheint nur in der Listenansicht, nicht in den Bearbeitungsformularen.
     *
     * @return void
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'config.onload')]
    public function addUsageHint(): void
    {
        if ('' !== (string) Input::get('act')) {
            return;
        }

        Message::addInfo(
            $GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['usageHint']
                ?? 'Ein Bereich erscheint erst dann im Backend-Menü, wenn ihm über „Module verwalten" mindestens ein Modul zugeordnet wurde.'
        );
    }

    /**
     * Füllt die Spalten der Bereichs-Übersicht.
     *
     * Nötig, weil das Icon je nach gewähltem Typ entweder aus einer der
     * Bibliotheken oder aus einer eigenen Datei stammt und die Rohwerte
     * (Icon-Name bzw. binäre UUID) für sich genommen unlesbar wären.
     *
     * @param array $row   Der Datensatz aus tl_backendmenue_bereiche
     * @param string $label Das von Contao vorbereitete Label (ungenutzt)
     * @param mixed $dc     Der Data Container (ungenutzt)
     * @param array $args   Die vorbelegten Spaltenwerte
     *
     * @return array Die Spaltenwerte: Name, Icon-Bezeichnung, Position
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'list.label.label')]
    public function formatAreaLabel(array $row, string $label, $dc, array $args): array
    {
        $args[1] = $this->describeIcon($row);
        $args[2] = $this->describeScope($row);

        return $args;
    }

    /**
     * Liefert die Optionen für das Icon-Auswahlfeld eines Bereichs.
     *
     * @return array Icons gruppiert nach Icon-Satz
     */
    #[AsCallback(table: 'tl_backendmenue_bereiche', target: 'fields.icon.options')]
    public function getIconOptions(): array
    {
        return $this->iconProvider->getIconsForDca();
    }

    /**
     * Liefert die Liste aller registrierten Backend-Module als Auswahl-Optionen.
     *
     * Iteriert über $GLOBALS['BE_MOD'] und gruppiert die Module nach ihrem
     * aktuellen Menübereich, damit die Auswahl im Backend nachvollziehbar bleibt.
     *
     * @return array Modul-Namen gruppiert nach Bereichs-Schlüssel
     */
    #[AsCallback(table: 'tl_backendmenue_zuordnungen', target: 'fields.module.options')]
    public function getModuleOptions(): array
    {
        $options = [];

        foreach (($GLOBALS['BE_MOD'] ?? []) as $group => $modules) {
            if (!\is_array($modules)) {
                continue;
            }

            foreach (array_keys($modules) as $moduleName) {
                $groupLabel = $GLOBALS['TL_LANG']['MOD'][$group] ?? $group;

                if (\is_array($groupLabel)) {
                    $groupLabel = $groupLabel[0] ?? $group;
                }

                $options[$groupLabel][$moduleName] = $GLOBALS['TL_LANG']['MOD'][$moduleName][0] ?? $moduleName;
            }
        }

        return $options;
    }

    /**
     * Beschriftet eine Modul-Zuordnung in der Eltern-Ansicht.
     *
     * Die Reihenfolge wird per Drag & Drop festgelegt, deshalb steht hier keine
     * Positionsnummer mehr — nur der Klarname des Moduls und sein technischer
     * Schlüssel. Das umgebende Markup setzt Contao selbst, hier kommt nur der
     * Inhalt zurück.
     *
     * Die beiden Contao-Fassungen rufen diesen Callback im Eltern-Modus mit
     * unterschiedlich vielen Argumenten auf (4.13 mit drei, 5.7 mit vier, siehe
     * DataContainer::generateRecordLabel) — $dc und $args sind deshalb optional.
     *
     * @param array $row  Die Datensatz-Zeile aus tl_backendmenue_zuordnungen
     * @param string $label Das von Contao aus list.label.fields gebaute Label (ungenutzt)
     * @param mixed $dc   Der Data Container; fehlt unter Contao 4.13
     * @param array $args Die Spaltenwerte; fehlen unter Contao 4.13
     *
     * @return string Das Label als HTML
     */
    #[AsCallback(table: 'tl_backendmenue_zuordnungen', target: 'list.label.label')]
    public function formatAssignmentLabel(array $row, string $label = '', $dc = null, array $args = []): string
    {
        $moduleLabel = $GLOBALS['TL_LANG']['MOD'][$row['module']][0] ?? $row['module'];

        return StringUtil::specialchars($moduleLabel)
            . ' <span class="tl_gray">[' . StringUtil::specialchars((string) $row['module']) . ']</span>';
    }

    /**
     * Sammelt die Bereichs-IDs, die die aktuelle Anfrage betrifft.
     *
     * Betrifft die Anfrage die Kindtabelle, ist dieser Callback nicht zuständig
     * und liefert eine leere Liste — die ID bezeichnet dann eine Zuordnung und
     * würde hier fälschlich als Bereich geprüft.
     *
     * @return array Liste von Bereichs-IDs aus Adresse und Formulardaten
     */
    private function getRequestedIds(): array
    {
        $table = (string) Input::get('table');

        if ('' !== $table && 'tl_backendmenue_bereiche' !== $table) {
            return [];
        }

        $ids = [];
        $id = (int) Input::get('id');

        if ($id > 0) {
            $ids[] = $id;
        }

        // Sammelaktionen wie "Mehrere bearbeiten" übergeben die Auswahl per POST
        foreach ((array) Input::post('IDS') as $postId) {
            if ((int) $postId > 0) {
                $ids[] = (int) $postId;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Sammelt die Bereiche, zu denen die angefragten Modul-Zuordnungen gehören.
     *
     * Je nach Aktion bezeichnet die ID in der Adresse entweder den Bereich
     * (Listen- und Sammelaktionen) oder eine einzelne Zuordnung, deren Bereich
     * erst nachgeschlagen werden muss. Beim Einfügen hängt die Bedeutung von
     * "pid" am Modus: 1 bedeutet "nach dieser Zuordnung", alles andere
     * "in diesen Bereich".
     *
     * @return array Liste von Bereichs-IDs
     */
    private function getRequestedAreaIdsForAssignments(): array
    {
        if ('tl_backendmenue_zuordnungen' !== (string) Input::get('table')) {
            return [];
        }

        $act = (string) Input::get('act');
        $id = (int) Input::get('id');
        $pid = (int) Input::get('pid');

        $areaIds = [];
        $assignmentIds = [];

        if (\in_array($act, ['edit', 'delete', 'show', 'cut', 'copy', 'toggle'], true)) {
            if ($id > 0) {
                $assignmentIds[] = $id;
            }
        } elseif ($id > 0) {
            $areaIds[] = $id;
        }

        if ($pid > 0) {
            if ('1' === (string) Input::get('mode')) {
                $assignmentIds[] = $pid;
            } else {
                $areaIds[] = $pid;
            }
        }

        foreach ((array) Input::post('IDS') as $postId) {
            if ((int) $postId > 0) {
                $assignmentIds[] = (int) $postId;
            }
        }

        return array_values(array_unique(array_merge($areaIds, $this->resolveAreaIds($assignmentIds))));
    }

    /**
     * Schlägt zu Zuordnungs-IDs die Bereiche nach, zu denen sie gehören.
     *
     * @param array $assignmentIds Liste von IDs aus tl_backendmenue_zuordnungen
     *
     * @return array Liste der zugehörigen Bereichs-IDs; Zuordnungen, die es
     *               nicht mehr gibt, liefern keinen Eintrag
     */
    private function resolveAreaIds(array $assignmentIds): array
    {
        if ([] === $assignmentIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, \count($assignmentIds), '?'));

        $result = Database::getInstance()
            ->prepare('SELECT DISTINCT pid FROM tl_backendmenue_zuordnungen WHERE id IN (' . $placeholders . ')')
            ->execute(...$assignmentIds);

        $areaIds = [];

        while ($result->next()) {
            $areaIds[] = (int) $result->pid;
        }

        return $areaIds;
    }

    /**
     * Beschreibt das Icon eines Bereichs in lesbarer Form.
     *
     * @param array $row Der Datensatz aus tl_backendmenue_bereiche
     *
     * @return string Die Icon-Bezeichnung, der Dateipfad, oder ein Hinweis
     *                wenn keine gültige Auswahl vorliegt
     */
    private function describeIcon(array $row): string
    {
        if ('file' === ($row['iconType'] ?? 'library')) {
            $path = $this->resolveIconFile($row['iconFile'] ?? null);

            return $path ?? '—';
        }

        $icons = $this->iconProvider->getAllIcons();

        return $icons[$row['icon'] ?? ''] ?? ($row['icon'] ?? '—');
    }

    /**
     * Beschreibt die Geltung eines Bereichs in lesbarer Form.
     *
     * Bei persönlichen Bereichen werden die Namen der betroffenen Benutzer
     * genannt, damit in der Übersicht ohne Umweg erkennbar ist, wen ein Bereich
     * betrifft. Benutzer, die es nicht mehr gibt, erscheinen als ihre ID.
     *
     * @param array $row Der Datensatz aus tl_backendmenue_bereiche
     *
     * @return string Klartext, z. B. "Global" oder "Hans Meier, Martin Schulz"
     */
    private function describeScope(array $row): string
    {
        $reference = $GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['scopes'] ?? [];

        if ('user' !== ($row['scope'] ?? 'global')) {
            return $reference['global'] ?? 'global';
        }

        $userIds = array_map('intval', StringUtil::deserialize($row['users'] ?? null, true));

        if ([] === $userIds) {
            return $reference['user'] ?? 'user';
        }

        $this->framework->initialize();
        $adapter = $this->framework->getAdapter(UserModel::class);
        $names = [];

        foreach ($userIds as $userId) {
            $user = $adapter->findById($userId);
            $names[] = null !== $user ? $user->name : '#' . $userId;
        }

        return implode(', ', $names);
    }

    /**
     * Löst die UUID einer Icon-Datei in einen Pfad auf.
     *
     * @param mixed $uuid Die binäre UUID aus dem fileTree-Feld, oder null
     *
     * @return string|null Der Pfad relativ zum Projektverzeichnis
     *                     (z. B. "files/backend/logo.svg"), oder null wenn
     *                     keine UUID gesetzt ist oder die Datei fehlt
     */
    private function resolveIconFile($uuid): ?string
    {
        if (empty($uuid)) {
            return null;
        }

        $this->framework->initialize();

        $model = $this->framework->getAdapter(FilesModel::class)->findByUuid($uuid);

        return $model?->path;
    }
}
