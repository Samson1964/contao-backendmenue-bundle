<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_backendmenue_bereiche'] = [
    'config' => [
        // FQCN statt Kurzname 'Table': Der Kurzname wurde in Contao 5 entfernt,
        // die Klasse DC_Table gibt es in 4.13 und 5.x gleichermaßen
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_backendmenue_zuordnungen'],
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['position'],
            'flag' => 11,
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['name', 'icon', 'position'],
            'showColumns' => true,
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations' => [
            // Konvention: 'edit' springt in die Kindtabelle, 'editheader' bearbeitet
            // den Datensatz selbst (keine 'children'-Operation, siehe Projektrichtlinien)
            'edit' => [
                'href' => 'table=tl_backendmenue_zuordnungen',
                'icon' => 'edit.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['edit'],
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['editheader'],
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['delete'],
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset();"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['show'],
            ],
        ],
    ],
    'palettes' => [
        'default' => '{general_legend},name,icon,position',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'name' => [
            'inputType' => 'text',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['name'],
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'icon' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['icon'],
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
                'chosen' => true,
                'includeBlankOption' => true,
            ],
            // Die Optionen liefert DcaCallbackListener::getIconOptions() (per #[AsCallback])
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'position' => [
            'inputType' => 'text',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['position'],
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'natural',
                'tl_class' => 'w50',
            ],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];
