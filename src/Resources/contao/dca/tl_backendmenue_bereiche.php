<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_backendmenue_bereiche'] = [
    'config' => [
        'dataContainer' => 'Table',
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
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['name', 'icon'],
            'showColumns' => true,
            'format' => '%s [%s]',
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
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['edit'],
            ],
            'children' => [
                'href' => 'table=tl_backendmenue_zuordnungen',
                'icon' => 'edit.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['children'],
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'label' => &$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['delete'],
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset();"',
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
            'options_callback' => ['tl_backendmenue_bereiche', 'getAvailableIcons'],
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

/**
 * Stellt Callback-Methoden für die DCA bereit.
 */
class tl_backendmenue_bereiche extends \Contao\Backend
{
    /**
     * Gibt die verfügbaren Icons für den Icon-Picker zurück.
     *
     * Kombiniert Font Awesome 6 und Contao Standard Icons.
     *
     * @return array Icons gruppiert nach Kategorie
     */
    public function getAvailableIcons(): array
    {
        if (!class_exists('\Schachbulle\BackendMenueBundle\Service\IconProvider')) {
            return [];
        }

        $iconProvider = new \Schachbulle\BackendMenueBundle\Service\IconProvider();

        return $iconProvider->getIconsForDca();
    }
}
