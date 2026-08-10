<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

use Contao\Database;

/**
 * Service zur Manipulation der Backend-Menüstruktur basierend auf konfigurierten Bereichen.
 *
 * Lädt benutzerdefinierte Menübereiche aus der Datenbank und ordnet Backend-Module
 * entsprechend um. Standardbereiche bleiben erhalten; einzelne Module können in neue
 * Bereiche verschoben werden. Die neuen Bereiche werden — sortiert nach ihrer
 * Position — hinter den Standardbereichen eingehängt.
 */
class BackendMenuManipulator
{
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

            $areas = $this->loadCustomAreas($db);
            $assignments = $this->loadAssignments($db);
        } catch (\Throwable) {
            return;
        }

        if ([] === $areas || [] === $assignments) {
            return;
        }

        // Erst alle Modul-Konfigurationen einsammeln und aus den Standardbereichen
        // lösen — in dieser Reihenfolge, weil nach dem Entfernen nichts mehr auffindbar wäre
        $extracted = $this->extractAssignedModules($assignments);

        $this->appendCustomAreas($areas, $assignments, $extracted);
        $this->removeEmptyGroups();
    }

    /**
     * Lädt alle benutzerdefinierten Menübereiche aus der Datenbank.
     *
     * @param Database $db Die Contao-Datenbankverbindung
     *
     * @return array Bereiche nach Position sortiert, Schlüssel = ID,
     *               Werte: ['name' => ..., 'icon' => ..., 'position' => ...]
     */
    private function loadCustomAreas(Database $db): array
    {
        $result = $db->execute('SELECT id, name, icon, position FROM tl_backendmenue_bereiche ORDER BY position, id');

        $areas = [];

        while ($result->next()) {
            $areas[(int) $result->id] = [
                'name' => (string) $result->name,
                'icon' => (string) $result->icon,
                'position' => (int) $result->position,
            ];
        }

        return $areas;
    }

    /**
     * Lädt alle Modul-Zuordnungen, sortiert nach Bereich und Position.
     *
     * @param Database $db Die Contao-Datenbankverbindung
     *
     * @return array Liste von ['module' => ..., 'area_id' => ..., 'position' => ...]
     */
    private function loadAssignments(Database $db): array
    {
        $result = $db->execute(
            'SELECT z.module, z.pid AS area_id, z.position
             FROM tl_backendmenue_zuordnungen z
             INNER JOIN tl_backendmenue_bereiche b ON z.pid = b.id
             ORDER BY b.position, z.position, z.id'
        );

        $assignments = [];

        while ($result->next()) {
            $assignments[] = [
                'module' => (string) $result->module,
                'area_id' => (int) $result->area_id,
                'position' => (int) $result->position,
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
                    break;
                }
            }
        }

        return $extracted;
    }

    /**
     * Hängt die benutzerdefinierten Bereiche mit ihren Modulen ans Menü an.
     *
     * Registriert zugleich die Anzeigenamen der Bereiche in TL_LANG, weil es
     * für dynamische Bereiche naturgemäß keine Sprachdatei gibt.
     *
     * @param array $areas       Die Bereiche aus loadCustomAreas()
     * @param array $assignments Die Zuordnungen aus loadAssignments()
     * @param array $extracted   Die eingesammelten Modul-Konfigurationen
     *
     * @return void
     */
    private function appendCustomAreas(array $areas, array $assignments, array $extracted): void
    {
        foreach ($areas as $areaId => $area) {
            $groupKey = 'backendmenue_' . $areaId;
            $groupModules = [];

            foreach ($assignments as $assignment) {
                if ($assignment['area_id'] === $areaId && isset($extracted[$assignment['module']])) {
                    $groupModules[$assignment['module']] = $extracted[$assignment['module']];
                }
            }

            // Bereiche ohne auffindbare Module nicht anlegen — eine leere
            // Gruppe würde im Backend nur als toter Menüpunkt erscheinen
            if ([] === $groupModules) {
                continue;
            }

            $GLOBALS['BE_MOD'][$groupKey] = $groupModules;
            $GLOBALS['TL_LANG']['MOD'][$groupKey] = [$area['name']];
        }
    }

    /**
     * Entfernt Standardbereiche, die nach dem Umhängen keine Module mehr enthalten.
     *
     * @return void
     */
    private function removeEmptyGroups(): void
    {
        foreach ($GLOBALS['BE_MOD'] as $group => $modules) {
            if (\is_array($modules) && [] === $modules) {
                unset($GLOBALS['BE_MOD'][$group]);
            }
        }
    }
}
