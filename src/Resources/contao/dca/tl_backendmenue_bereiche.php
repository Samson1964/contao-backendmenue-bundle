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
            // Die Spaltenwerte setzt DcaCallbackListener::formatAreaLabel() (per #[AsCallback]),
            // weil das Icon je nach Typ aus der Bibliothek oder aus einer Datei stammt
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
        '__selector__' => ['iconType'],
        'default' => '{general_legend},name,position;{icon_legend},iconType,iconColor',
    ],
    'subpalettes' => [
        'iconType_library' => 'icon',
        'iconType_file' => 'iconFile',
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
        'iconType' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconType'],
            'options' => ['library', 'file'],
            'reference' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconTypes'],
            'eval' => [
                'submitOnChange' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(16) NOT NULL default 'library'",
        ],
        'icon' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['icon'],
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50 clr',
                'chosen' => true,
                'includeBlankOption' => true,
            ],
            // Die Optionen liefert DcaCallbackListener::getIconOptions() (per #[AsCallback])
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'iconFile' => [
            'inputType' => 'fileTree',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconFile'],
            'eval' => [
                'mandatory' => true,
                'filesOnly' => true,
                'fieldType' => 'radio',
                'extensions' => 'svg,png,gif',
                'tl_class' => 'clr',
            ],
            'sql' => 'binary(16) NULL',
        ],
        'iconColor' => [
            'inputType' => 'text',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconColor'],
            'eval' => [
                'maxlength' => 6,
                'colorpicker' => true,
                'isHexColor' => true,
                'decodeEntities' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => "varchar(6) NOT NULL default ''",
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
