<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Service;

use Contao\BackendUser;
use Contao\Database;

/**
 * Service zur Manipulation der Backend-Menüstruktur basierend auf konfigurierten Bereichen.
 *
 * Lädt benutzerdefinierte Menübereiche aus der Datenbank und ordnet Backend-Module
 * entsprechend an. Standardbereiche bleiben erhalten, einzelne Module können in neue
 * Bereiche verschoben werden.
 */
class BackendMenuManipulator
{
    /**
     * Manipuliert die globale BE_MOD-Struktur entsprechend den konfigurierten Bereichen.
     *
     * Diese Methode wird beim Backend-Laden aufgerufen und rekonstruiert das Menü
     * basierend auf den Einträgen aus tl_backendmenue_bereiche und tl_backendmenue_zuordnungen.
     *
     * @return void
     */
    public function manipulateBackendMenu(): void
    {
        if (!\is_array($GLOBALS['BE_MOD'])) {
            return;
        }

        // Lade die benutzerdefinierten Bereiche
        $customAreas = $this->loadCustomAreas();

        if (empty($customAreas)) {
            return;
        }

        // Lade die Modul-Zuordnungen
        $assignments = $this->loadAssignments();

        if (empty($assignments)) {
            return;
        }

        // Entferne zugeordnete Module aus ihren Standardbereichen
        $this->removeAssignedModules($assignments);

        // Füge die Module in die benutzerdefinierten Bereiche ein
        $this->assignModulesToCustomAreas($customAreas, $assignments);
    }

    /**
     * Lädt alle benutzerdefinierten Menübereiche aus der Datenbank.
     *
     * @return array Array von Bereichen mit Struktur: ['name' => '...', 'icon' => '...', 'position' => ...]
     */
    private function loadCustomAreas(): array
    {
        $result = Database::getInstance()
            ->prepare('SELECT * FROM tl_backendmenue_bereiche ORDER BY position, sorting')
            ->execute();

        $areas = [];

        while ($result->next()) {
            $areas[$result->name] = [
                'icon' => $result->icon,
                'position' => $result->position,
                'id' => $result->id,
            ];
        }

        return $areas;
    }

    /**
     * Lädt alle Modul-Zuordnungen zu benutzerdefinierten Bereichen.
     *
     * @return array Array mit Struktur: ['module_name' => ['bereich' => '...', 'position' => ...], ...]
     */
    private function loadAssignments(): array
    {
        $result = Database::getInstance()
            ->prepare(
                'SELECT z.module, b.name AS bereich, z.position
                 FROM tl_backendmenue_zuordnungen z
                 LEFT JOIN tl_backendmenue_bereiche b ON z.pid = b.id
                 ORDER BY b.position, z.position'
            )
            ->execute();

        $assignments = [];

        while ($result->next()) {
            if ($result->bereich) {
                $assignments[$result->module] = [
                    'bereich' => $result->bereich,
                    'position' => $result->position,
                ];
            }
        }

        return $assignments;
    }

    /**
     * Entfernt Module aus ihren Standardbereichen, die jetzt in benutzerdefinierten Bereichen sind.
     *
     * @param array $assignments Die Modul-Zuordnungen
     *
     * @return void
     */
    private function removeAssignedModules(array $assignments): void
    {
        foreach ($GLOBALS['BE_MOD'] as $group => $modules) {
            if (!\is_array($modules)) {
                continue;
            }

            foreach ($modules as $moduleName => $moduleConfig) {
                if (isset($assignments[$moduleName])) {
                    unset($GLOBALS['BE_MOD'][$group][$moduleName]);
                }
            }

            // Entferne leere Bereiche
            if (empty($GLOBALS['BE_MOD'][$group])) {
                unset($GLOBALS['BE_MOD'][$group]);
            }
        }
    }

    /**
     * Ordnet Module in benutzerdefinierten Bereichen an.
     *
     * @param array $customAreas Die benutzerdefinierten Bereiche
     * @param array $assignments Die Modul-Zuordnungen
     *
     * @return void
     */
    private function assignModulesToCustomAreas(array $customAreas, array $assignments): void
    {
        // Sortiere Bereiche nach Position
        \uasort($customAreas, static function (array $a, array $b): int {
            return $a['position'] <=> $b['position'];
        });

        foreach ($customAreas as $areaName => $areaConfig) {
            if (!isset($GLOBALS['BE_MOD'][$areaName])) {
                $GLOBALS['BE_MOD'][$areaName] = [];
            }

            // Sammle Module für diesen Bereich
            $areaModules = [];

            foreach ($assignments as $moduleName => $assignment) {
                if ($assignment['bereich'] === $areaName) {
                    // Hole die Modul-Konfiguration aus einem anderen Bereich
                    $moduleConfig = $this->findModuleConfig($moduleName);

                    if ($moduleConfig !== null) {
                        $areaModules[$assignment['position']][$moduleName] = $moduleConfig;
                    }
                }
            }

            // Sortiere Module nach Position und füge sie zum Bereich hinzu
            \ksort($areaModules);

            foreach ($areaModules as $modules) {
                foreach ($modules as $moduleName => $moduleConfig) {
                    $GLOBALS['BE_MOD'][$areaName][$moduleName] = $moduleConfig;
                }
            }
        }
    }

    /**
     * Sucht eine Modul-Konfiguration in der aktuellen BE_MOD-Struktur.
     *
     * @param string $moduleName Der Name des Moduls
     *
     * @return array|null Die Modul-Konfiguration oder null wenn nicht gefunden
     */
    private function findModuleConfig(string $moduleName): ?array
    {
        foreach ($GLOBALS['BE_MOD'] as $group => $modules) {
            if (\is_array($modules) && isset($modules[$moduleName])) {
                return $modules[$moduleName];
            }
        }

        return null;
    }
}
