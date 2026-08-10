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
            'fields' => ['position'],
            'panelLayout' => 'search,limit',
            'headerFields' => ['name', 'icon'],
            // child_record_callback liefert DcaCallbackListener::listAssignment() (per #[AsCallback])
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
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset();"',
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
