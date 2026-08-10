<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\Attributes\AsHook;
use Schachbulle\BackendMenueBundle\Service\IconProvider;

/**
 * Listener für das Laden von Datencontainern.
 *
 * Registriert Callbacks für DCA-Felder, die auf Services zugreifen müssen.
 */
#[AsHook('loadDataContainer')]
class LoadDataContainerListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param IconProvider $iconProvider Der Service für Icon-Verwaltung
     */
    public function __construct(
        private readonly IconProvider $iconProvider,
    ) {
    }

    /**
     * Wird beim Laden eines Datencontainers aufgerufen.
     *
     * Registriert dynamische Callbacks für die Icon-Optionen.
     *
     * @param string $dataContainer Der Name des zu ladenden Datencontainers
     *
     * @return void
     */
    public function __invoke(string $dataContainer): void
    {
        if ($dataContainer !== 'tl_backendmenue_bereiche') {
            return;
        }

        // Registriere die Icon-Optionen für das icon-Feld
        if (isset($GLOBALS['TL_DCA'][$dataContainer]['fields']['icon'])) {
            $GLOBALS['TL_DCA'][$dataContainer]['fields']['icon']['options'] = $this->iconProvider->getIconsForDca();
        }

        // Registriere die Modul-Optionen für tl_backendmenue_zuordnungen
        if (isset($GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen']['fields']['module'])) {
            $GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen']['fields']['module']['options'] = $this->getBackendModules();
        }
    }

    /**
     * Gibt eine Liste der verfügbaren Backend-Module zurück.
     *
     * @return array Backend-Modul-Namen
     */
    private function getBackendModules(): array
    {
        $modules = [];

        if (isset($GLOBALS['BE_MOD']) && \is_array($GLOBALS['BE_MOD'])) {
            foreach ($GLOBALS['BE_MOD'] as $group => $groupModules) {
                if (\is_array($groupModules)) {
                    foreach ($groupModules as $moduleName => $moduleConfig) {
                        $modules[$moduleName] = $moduleName;
                    }
                }
            }
        }

        \asort($modules);

        return $modules;
    }
}
