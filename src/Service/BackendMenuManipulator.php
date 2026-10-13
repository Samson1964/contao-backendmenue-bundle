<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

use Contao\Database;

/**
 * Service zur Manipulation der Backend-Menüstruktur basierend auf konfigurierten Bereichen.
 *
 * Lädt die für den angemeldeten Benutzer geltenden Menübereiche und ordnet die
 * Backend-Module entsprechend um. Standardbereiche bleiben erhalten; einzelne
 * Module werden in die eigenen Bereiche verschoben und diese an ihrer
 * Wunschposition einsortiert.
 *
 * Welche Bereiche gelten, entscheidet der AreaAccess: die global definierten
 * (sofern der Benutzer sie nicht ausgeblendet hat) und seine persönlichen.
 */
class BackendMenuManipulator
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param AreaAccess $access Liefert die für den Benutzer geltenden Bereiche
     */
    public function __construct(
        private readonly AreaAccess $access,
    ) {
    }

    /**
     * Nach der Manipulation tatsächlich angelegte Bereiche.
     *
     * Wird vom BackendAssetsListener ausgelesen, um die Icon-CSS-Regeln für
     * genau diese Gruppen zu erzeugen. Aufbau je Eintrag:
     * ['iconType' => 'library'|'file', 'icon' => …, 'iconFile' => UUID|null, 'iconColor' => …]
     */
    private array $appliedAreas = [];

    /**
     * Standardbereiche, aus denen Module herausgenommen wurden.
     *
     * Nur diese werden am Ende auf Brauchbarkeit geprüft — fremde Gruppen,
     * die schon vorher niemandem etwas zeigten, bleiben unangetastet.
     */
    private array $touchedGroups = [];

    /**
     * Gibt die im aktuellen Request angelegten Bereiche zurück.
     *
     * @return array Gruppenschlüssel (z. B. "backendmenue_3") => Icon-Angaben
     */
    public function getAppliedAreas(): array
    {
        return $this->appliedAreas;
    }

    /**
     * Manipuliert die globale BE_MOD-Struktur entsprechend den konfigurierten Bereichen.
     *
     * Als BE_MOD-Gruppenschlüssel dient "backendmenue_<id>" statt des Bereichsnamens,
     * weil der Schlüssel im Backend als CSS-Klasse landet und Leerzeichen oder
     * Umlaute dort das Markup zerstören würden. Der Anzeigename wird über
     * $GLOBALS['TL_LANG']['MOD'] registriert.
     *
     * Datenbankfehler (Tabellen fehlen noch, kein Verbindungsaufbau möglich) werden
     * bewusst geschluckt: Das Menü bleibt dann einfach unverändert, statt das
     * gesamte Backend lahmzulegen.
     *
     * @return void
     */
    public function manipulateBackendMenu(): void
    {
        if (!\is_array($GLOBALS['BE_MOD'] ?? null)) {
            return;
        }

        try {
            $db = Database::getInstance();

            // Vor der ersten Migration existieren die Tabellen noch nicht —
            // dann darf das Backend trotzdem nutzbar bleiben
            if (!$db->tableExists('tl_backendmenue_bereiche') || !$db->tableExists('tl_backendmenue_zuordnungen')) {
                return;
            }

            // Nur die Bereiche, die für diesen Benutzer gelten
            $areas = $this->access->getAreasForMenu();
            $assignments = $this->loadAssignments($db, array_keys($areas));
        } catch (\Throwable) {
            return;
        }

        if ([] === $areas || [] === $assignments) {
            return;
        }

        // Erst alle Modul-Konfigurationen einsammeln und aus den Standardbereichen
        // lösen — in dieser Reihenfolge, weil nach dem Entfernen nichts mehr auffindbar wäre
        $extracted = $this->extractAssignedModules($assignments);

        $this->insertCustomAreas($areas, $assignments, $extracted);
        $this->removeEmptyGroups();
    }

    /**
     * Lädt die Modul-Zuordnungen der übergebenen Bereiche.
     *
     * Die Reihenfolge stammt aus dem Feld 'sorting', das im Backend per
     * Drag & Drop gepflegt wird.
     *
     * @param Database $db      Die Contao-Datenbankverbindung
     * @param array    $areaIds Die Bereiche, deren Zuordnungen gelten sollen;
     *                          bei leerer Liste wird nichts geladen
     *
     * @return array Liste von ['module' => ..., 'area_id' => ...]
     */
    private function loadAssignments(Database $db, array $areaIds): array
    {
        if ([] === $areaIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, \count($areaIds), '?'));

        $result = $db
            ->prepare(
                'SELECT z.module, z.pid AS area_id
                 FROM tl_backendmenue_zuordnungen z
                 INNER JOIN tl_backendmenue_bereiche b ON z.pid = b.id
                 WHERE z.pid IN (' . $placeholders . ')
                 ORDER BY b.position, z.sorting, z.id'
            )
            ->execute(...$areaIds);

        $assignments = [];

        while ($result->next()) {
            $assignments[] = [
                'module' => (string) $result->module,
                'area_id' => (int) $result->area_id,
            ];
        }

        return $assignments;
    }

    /**
     * Sucht die zugeordneten Module in BE_MOD, merkt sich ihre Konfiguration
     * und entfernt sie aus ihren bisherigen Bereichen.
     *
     * @param array $assignments Die Modul-Zuordnungen aus loadAssignments()
     *
     * @return array Modul-Name => ursprüngliche Modul-Konfiguration;
     *               nicht (mehr) existierende Module fehlen im Ergebnis
     */
    private function extractAssignedModules(array $assignments): array
    {
        $extracted = [];

        foreach ($assignments as $assignment) {
            $moduleName = $assignment['module'];

            foreach ($GLOBALS['BE_MOD'] as $group => $modules) {
                if (\is_array($modules) && isset($modules[$moduleName])) {
                    $extracted[$moduleName] = $modules[$moduleName];
                    unset($GLOBALS['BE_MOD'][$group][$moduleName]);
                    $this->touchedGroups[$group] = true;
                    break;
                }
            }
        }

        return $extracted;
    }

    /**
     * Fügt die benutzerdefinierten Bereiche an ihrer Wunschposition ins Menü ein.
     *
     * Die Position zählt über das Gesamtmenü: 1 setzt den Bereich ganz nach
     * oben (vor die Standardbereiche), 2 an die zweite Stelle usw.; Werte
     * über der Gruppenanzahl hängen den Bereich ans Ende. Registriert zugleich
     * die Anzeigenamen der Bereiche in TL_LANG, weil es für dynamische
     * Bereiche naturgemäß keine Sprachdatei gibt.
     *
     * @param array $areas       Die Bereiche aus loadCustomAreas()
     * @param array $assignments Die Zuordnungen aus loadAssignments()
     * @param array $extracted   Die eingesammelten Modul-Konfigurationen
     *
     * @return void
     */
    private function insertCustomAreas(array $areas, array $assignments, array $extracted): void
    {
        // Neue Gruppen samt Modulen aufbauen; Bereiche ohne auffindbare Module
        // überspringen — eine leere Gruppe würde Contao ohnehin ausblenden
        $newGroups = [];

        foreach ($areas as $areaId => $area) {
            $groupKey = 'backendmenue_' . $areaId;
            $groupModules = [];

            foreach ($assignments as $assignment) {
                if ($assignment['area_id'] === $areaId && isset($extracted[$assignment['module']])) {
                    $groupModules[$assignment['module']] = $extracted[$assignment['module']];
                }
            }

            if ([] === $groupModules) {
                continue;
            }

            $newGroups[$groupKey] = [
                'modules' => $groupModules,
                'position' => $area['position'],
            ];

            $GLOBALS['TL_LANG']['MOD'][$groupKey] = [$area['name']];

            $this->appliedAreas[$groupKey] = [
                'iconType' => $area['iconType'],
                'icon' => $area['icon'],
                'iconFile' => $area['iconFile'],
                'iconColor' => $area['iconColor'],
            ];
        }

        if ([] === $newGroups) {
            return;
        }

        // Reihenfolge bestimmen: bestehende Gruppen behalten ihre Ordnung,
        // jede neue Gruppe wird an ihrem (1-basierten) Wunschindex eingefügt
        $order = array_keys($GLOBALS['BE_MOD']);

        uasort($newGroups, static fn (array $a, array $b): int => $a['position'] <=> $b['position']);

        foreach ($newGroups as $groupKey => $group) {
            $index = min(max($group['position'], 1) - 1, \count($order));
            array_splice($order, $index, 0, [$groupKey]);
        }

        // BE_MOD in der neuen Reihenfolge zusammensetzen
        $reordered = [];

        foreach ($order as $groupKey) {
            $reordered[$groupKey] = $newGroups[$groupKey]['modules'] ?? $GLOBALS['BE_MOD'][$groupKey];
        }

        $GLOBALS['BE_MOD'] = $reordered;
    }

    /**
     * Entfernt Standardbereiche, die nach dem Umhängen keine Module mehr enthalten.
     *
     * @return void
     */
    private function removeEmptyGroups(): void
    {
        foreach ($GLOBALS['BE_MOD'] as $group => $modules) {
            if (!\is_array($modules)) {
                continue;
            }

            if ([] === $modules) {
                unset($GLOBALS['BE_MOD'][$group]);
                continue;
            }

            // Gruppen, aus denen Module herausgenommen wurden, können für
            // diesen Benutzer nur noch unsichtbare Module enthalten. Contao
            // legt solche Gruppen trotzdem an (BackendUser::navigation prüft
            // die Gruppe gegen die Benutzerrechte, füllt sie aber erst nach
            // der Sichtbarkeitsprüfung je Modul) und läuft anschließend im
            // BackendMainListener in eine Warnung, weil der Schlüssel
            // "modules" fehlt. Deshalb hier selbst aufräumen.
            if (!isset($this->touchedGroups[$group])) {
                continue;
            }

            foreach ($modules as $moduleName => $config) {
                if ($this->access->isModuleVisible((string) $moduleName, \is_array($config) ? $config : [])) {
                    continue 2;
                }
            }

            unset($GLOBALS['BE_MOD'][$group]);
        }
    }
}
