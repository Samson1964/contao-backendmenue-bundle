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
            'fields' => ['name', 'icon', 'scope', 'position'],
            'showColumns' => true,
            // Die Spaltenwerte setzt DcaCallbackListener::formatAreaLabel() (per #[AsCallback]),
            // weil das Icon je nach Typ aus der Bibliothek oder aus einer Datei stammt
            // und die Geltung als Klartext mit den betroffenen Benutzern erscheint
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        // 'primary' => true hält jedes Symbol dauerhaft in der Zeile sichtbar.
        // Hintergrund: Contao 5.7 zeigt nur primäre Operationen direkt an und
        // verschiebt alle übrigen ins Aufklappmenü, sobald überhaupt eine
        // Operation primär ist — und das Backend hängt bei sortierbaren Tabellen
        // selbst einen primären (unsichtbaren) Drag-Handle an. Contao 4.13 kennt
        // den Schlüssel nicht und ignoriert ihn.
        'operations' => [
            // Konvention: 'edit' springt in die Kindtabelle, 'editheader' bearbeitet
            // den Datensatz selbst (keine 'children'-Operation, siehe Projektrichtlinien)
            'edit' => [
                'href' => 'table=tl_backendmenue_zuordnungen',
                'icon' => 'edit.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['edit'],
                'primary' => true,
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['editheader'],
                'primary' => true,
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['delete'],
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset();"',
                'primary' => true,
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['show'],
                'primary' => true,
            ],
        ],
    ],
    'palettes' => [
        '__selector__' => ['iconType', 'scope'],
        // Die Geltungs-Legende blendet DcaCallbackListener::restrictToOwnAreas()
        // für Benutzer ohne Administratorrechte aus; deren Bereiche gehören
        // immer ihnen selbst und sind nicht umwidmbar.
        'default' => '{general_legend},name,position;{icon_legend},iconType,iconColor;{scope_legend},scope',
    ],
    'subpalettes' => [
        'iconType_library' => 'icon',
        'iconType_file' => 'iconFile',
        'scope_user' => 'users',
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
            'exclude' => false,
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
            'exclude' => false,
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
            'exclude' => false,
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
            'exclude' => false,
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
            'exclude' => false,
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
            'exclude' => false,
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'natural',
                'tl_class' => 'w50',
            ],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        // 'exclude' sperrt beide Felder für Benutzer ohne ausdrückliche
        // Freigabe — zusammen mit der gekürzten Palette verhindert das, dass
        // jemand seinen persönlichen Bereich global schaltet oder fremden
        // Benutzern zuschiebt (siehe DcaCallbackListener::restrictToOwnAreas)
        'scope' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['scope'],
            'exclude' => true,
            'options' => ['global', 'user'],
            'reference' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['scopes'],
            'eval' => [
                'submitOnChange' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(16) NOT NULL default 'global'",
        ],
        'users' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['users'],
            'exclude' => true,
            'foreignKey' => 'tl_user.name',
            'eval' => [
                'mandatory' => true,
                'multiple' => true,
                'chosen' => true,
                'tl_class' => 'clr',
            ],
            'sql' => 'blob NULL',
            'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
        ],
    ],
];
