<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen'] = [
    'config' => [
        // FQCN statt Kurzname 'Table': Der Kurzname wurde in Contao 5 entfernt,
        // die Klasse DC_Table gibt es in 4.13 und 5.x gleichermaßen
        'dataContainer' => DC_Table::class,
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
            // 'sorting' muss das erste Feld sein, sonst schaltet DC_Table die
            // Drag-&-Drop-Sortierung ab (DC_Table::parentView, 4.13 wie 5.x)
            'fields' => ['sorting'],
            'panelLayout' => 'search',
            'headerFields' => ['name'],
        ],
        'label' => [
            'fields' => ['module'],
            // Bewusst kein child_record_callback: Contao 5.7 hat ihn abgekündigt und
            // hängt bei seiner Nutzung einen als "primary" markierten Drag-Handle an
            // die Operationen — sobald eine Operation primär ist, verschiebt das
            // Backend alle übrigen Symbole ins Aufklappmenü. Das Label baut deshalb
            // DcaCallbackListener::formatAssignmentLabel() (per #[AsCallback]);
            // Contao 4.13 erreicht es über den else-Zweig von DC_Table::parentView().
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        // 'primary' => true hält jedes Symbol dauerhaft in der Zeile sichtbar —
        // sonst verschiebt Contao 5.7 sie ins Aufklappmenü, weil das Backend bei
        // sortierbaren Tabellen selbst einen primären Drag-Handle anhängt.
        // Contao 4.13 kennt den Schlüssel nicht und ignoriert ihn.
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['edit'],
                'primary' => true,
            ],
            // Ohne 'cut' fehlt der Drag-&-Drop-Sortierung ihr Unterbau: Contao
            // setzt beim Ablegen genau diese Aktion ab (act=paste&mode=cut)
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['cut'],
                'attributes' => 'onclick="Backend.getScrollOffset()"',
                'primary' => true,
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['delete'],
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset();"',
                'primary' => true,
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_zuordnungen']['show'],
                'primary' => true,
            ],
        ],
    ],
    'palettes' => [
        // Kein Positionsfeld mehr: Die Reihenfolge wird in der Übersicht
        // per Drag & Drop festgelegt und in 'sorting' gespeichert
        'default' => '{general_legend},module',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_backendmenue_bereiche.name',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
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
                'includeBlankOption' => true,
            ],
            // Die Optionen liefert DcaCallbackListener::getModuleOptions() (per #[AsCallback])
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
