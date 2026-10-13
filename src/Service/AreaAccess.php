<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

use Contao\BackendUser;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\StringUtil;

/**
 * Entscheidet, welche Menübereiche ein Backend-Benutzer sehen und bearbeiten darf.
 *
 * Es gibt zwei Arten von Bereichen: global definierte, die für alle Benutzer
 * gelten, und persönliche, die einem oder mehreren Benutzern zugeordnet sind.
 * Jeder Benutzer kann die globalen Bereiche für seinen Zugang ausblenden
 * (Feld "backendmenueHideGlobal" in tl_user).
 *
 * Die Zuordnung steckt im serialisierten Feld "users", weshalb hier in PHP
 * gefiltert wird statt in SQL: Eine Suche mit LIKE über serialisierte Daten
 * trifft auch Teilzahlen (die 1 steckt in der 21) und wäre damit falsch.
 * Die Zahl der Menübereiche bleibt überschaubar, sodass das nicht ins Gewicht fällt.
 */
class AreaAccess
{
    /**
     * Zwischenspeicher für die geladenen Bereiche, je Anfrage.
     *
     * Verwaltung und Menüaufbau fragen dieselben Daten mehrfach ab.
     */
    private array|null $areas = null;

    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param ContaoFramework $framework Zugriff auf BackendUser; der Dienst
     *                                   "security.helper" trägt in Contao 4.13
     *                                   und 5.x unterschiedliche Klassen, der
     *                                   Umweg über BackendUser gilt in beiden
     */
    public function __construct(
        private readonly ContaoFramework $framework,
    ) {
    }

    /**
     * Gibt an, ob der angemeldete Benutzer Administrator ist.
     *
     * Administratoren sehen und bearbeiten alle Bereiche, auch die persönlichen
     * anderer Benutzer.
     *
     * @return bool True für Administratoren, false für alle anderen und wenn
     *              niemand angemeldet ist
     */
    public function isAdmin(): bool
    {
        $user = $this->getUser();

        return null !== $user && (bool) $user->isAdmin;
    }

    /**
     * Gibt die ID des angemeldeten Backend-Benutzers zurück.
     *
     * @return int Die ID, oder 0 wenn niemand angemeldet ist
     */
    public function getUserId(): int
    {
        $user = $this->getUser();

        return null !== $user ? (int) $user->id : 0;
    }

    /**
     * Gibt an, ob der Benutzer die global definierten Bereiche ausgeblendet hat.
     *
     * @return bool True, wenn die globalen Bereiche für diesen Zugang nicht
     *              gelten sollen
     */
    public function hidesGlobalAreas(): bool
    {
        $user = $this->getUser();

        return null !== $user && (bool) $user->backendmenueHideGlobal;
    }

    /**
     * Gibt die IDs der Bereiche zurück, die der Benutzer verwalten darf.
     *
     * Administratoren dürfen alle verwalten; alle anderen nur die persönlichen
     * Bereiche, in denen sie selbst eingetragen sind. Globale Bereiche sind für
     * Nicht-Administratoren tabu, auch wenn sie im Menü erscheinen.
     *
     * @return array Liste von Bereichs-IDs; leer, wenn es nichts zu verwalten gibt
     */
    public function getEditableAreaIds(): array
    {
        $areas = $this->loadAreas();

        if ($this->isAdmin()) {
            return array_keys($areas);
        }

        $userId = $this->getUserId();
        $ids = [];

        foreach ($areas as $id => $area) {
            if ('user' === $area['scope'] && \in_array($userId, $area['users'], true)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Prüft, ob der Benutzer einen bestimmten Bereich verwalten darf.
     *
     * @param int $areaId Die ID des Bereichs
     *
     * @return bool True, wenn der Zugriff erlaubt ist
     */
    public function canEditArea(int $areaId): bool
    {
        return \in_array($areaId, $this->getEditableAreaIds(), true);
    }

    /**
     * Gibt die Bereiche zurück, die im Backend-Menü des Benutzers erscheinen.
     *
     * Das sind die persönlichen Bereiche des Benutzers und — sofern er sie nicht
     * ausgeblendet hat — die global definierten. Sortiert nach der Position und
     * bei Gleichstand nach ID, damit die Reihenfolge stabil bleibt.
     *
     * @return array Bereiche mit ihren Angaben, Schlüssel ist die Bereichs-ID
     */
    public function getAreasForMenu(): array
    {
        $userId = $this->getUserId();

        if (0 === $userId) {
            return [];
        }

        $showGlobal = !$this->hidesGlobalAreas();
        $result = [];

        foreach ($this->loadAreas() as $id => $area) {
            $isMine = 'user' === $area['scope'] && \in_array($userId, $area['users'], true);
            $isGlobal = 'global' === $area['scope'] && $showGlobal;

            if ($isMine || $isGlobal) {
                $result[$id] = $area;
            }
        }

        return $result;
    }

    /**
     * Gibt an, ob ein Backend-Modul für den Benutzer im Menü erscheint.
     *
     * Bildet die Bedingungen aus BackendUser::navigation() nach: Versteckte
     * Module tauchen nie auf, Module mit abgeschalteter Rechteprüfung immer,
     * alle anderen nur bei entsprechender Freigabe.
     *
     * @param string $module Der Name des Backend-Moduls
     * @param array  $config Die Modul-Konfiguration aus $GLOBALS['BE_MOD']
     *
     * @return bool True, wenn das Modul für diesen Benutzer sichtbar ist
     */
    public function isModuleVisible(string $module, array $config): bool
    {
        if ($config['hideInNavigation'] ?? false) {
            return false;
        }

        if ($config['disablePermissionChecks'] ?? false) {
            return true;
        }

        $user = $this->getUser();

        return null !== $user && (bool) $user->hasAccess($module, 'modules');
    }

    /**
     * Lädt alle Bereiche aus der Datenbank.
     *
     * Fehlt die Tabelle noch (vor der ersten Migration) oder scheitert der
     * Datenbankzugriff, wird eine leere Liste geliefert — das Backend bleibt
     * dann ohne eigene Menübereiche nutzbar, statt mit einem Fehler abzubrechen.
     *
     * @return array Bereiche mit den Schlüsseln name, iconType, icon, iconFile,
     *               iconColor, position, scope und users (Liste von Benutzer-IDs)
     */
    protected function loadAreas(): array
    {
        if (null !== $this->areas) {
            return $this->areas;
        }

        $this->areas = [];

        try {
            $db = Database::getInstance();

            if (!$db->tableExists('tl_backendmenue_bereiche')) {
                return $this->areas;
            }

            $result = $db->execute(
                'SELECT id, name, iconType, icon, iconFile, iconColor, position, scope, users
                 FROM tl_backendmenue_bereiche ORDER BY position, id'
            );

            while ($result->next()) {
                $this->areas[(int) $result->id] = [
                    'name' => (string) $result->name,
                    'iconType' => (string) ($result->iconType ?: 'library'),
                    'icon' => (string) $result->icon,
                    'iconFile' => $result->iconFile,
                    'iconColor' => (string) $result->iconColor,
                    'position' => (int) $result->position,
                    'scope' => 'user' === $result->scope ? 'user' : 'global',
                    'users' => array_map('intval', StringUtil::deserialize($result->users, true)),
                ];
            }
        } catch (\Throwable) {
            $this->areas = [];
        }

        return $this->areas;
    }

    /**
     * Gibt den angemeldeten Backend-Benutzer zurück.
     *
     * @return BackendUser|null Der Benutzer, oder null wenn niemand angemeldet
     *                          ist oder das Framework nicht bereitsteht
     */
    private function getUser(): BackendUser|null
    {
        try {
            // getInstance() statt createInstance(): Nur das Singleton trägt den
            // angemeldeten Benutzer, eine frische Instanz wäre leer
            $user = $this->framework->getAdapter(BackendUser::class)->getInstance();
        } catch (\Throwable) {
            return null;
        }

        return $user instanceof BackendUser && $user->id ? $user : null;
    }
}
