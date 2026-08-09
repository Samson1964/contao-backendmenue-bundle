<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_backendmenue_bereiche',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['position'],
            'panelLayout' => 'search,limit',
            'headerFields' => ['name', 'icon'],
            'child_record_callback' => ['tl_backendmenue_zuordnungen', 'listBackendModules'],
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['edit'],
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['delete'],
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset();"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['show'],
            ],
        ],
    ],
    'palettes' => [
        'default' => '{general_legend},module,position',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'module' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['module'],
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
                'chosen' => true,
            ],
            'options_callback' => ['tl_backendmenue_zuordnungen', 'getBackendModules'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'position' => [
            'inputType' => 'text',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['position'],
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'natural',
                'tl_class' => 'w50',
            ],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];

/**
 * Stellt Callback-Methoden für die DCA bereit.
 */
class tl_backendmenue_zuordnungen extends \Contao\Backend
{
    /**
     * Gibt die Liste der verfügbaren Backend-Module zurück.
     *
     * @return array Assoziatives Array mit Modul-Namen als Schlüssel und Wert
     */
    public function getBackendModules(): array
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

    /**
     * Rendert eine Modul-Zuordnung in der List-Ansicht.
     *
     * @param array $row Die Datensatz-Zeile aus der Datenbank
     *
     * @return string HTML für den List-Eintrag
     */
    public function listBackendModules(array $row): string
    {
        return '<div class="tl_content_left">' . $row['module'] . ' <span style="color:#999;padding-left:3px;">(Position: ' . $row['position'] . ')</span></div>';
    }
}
