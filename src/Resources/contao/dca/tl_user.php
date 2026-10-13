<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Erweitert die Backend-Benutzer um den Schalter, mit dem sich die global
 * definierten Menübereiche für den eigenen Zugang ausblenden lassen.
 */
// Bewusst ohne 'exclude': Das Feld ist eine persönliche Einstellung wie die
// Sprache oder die Hilfetexte und muss vom Benutzer im eigenen Profil
// gespeichert werden können. Mit 'exclude' => true nimmt Contao die Eingabe
// eines Benutzers ohne Feldrechte stillschweigend nicht an. Die Core-Felder
// desselben Abschnitts (useCE, showHelp, doNotCollapse) führen es ebenfalls nicht.
$GLOBALS['TL_DCA']['tl_user']['fields']['backendmenueHideGlobal'] = [
    'inputType' => 'checkbox',
    'label' => &$GLOBALS['TL_LANG']['tl_user']['backendmenueHideGlobal'],
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];

/**
 * Das Feld in jede Palette einhängen, die den Backend-Abschnitt führt.
 *
 * Das betrifft sowohl das eigene Profil (Palette "login") als auch die
 * Benutzerverwaltung (Paletten "admin", "default", "group", "extend",
 * "custom"). Als Anker dient "useCE", weil dieses Feld in Contao 4.13 und
 * 5.x in allen genannten Paletten vorkommt — die Feldlisten unterscheiden
 * sich sonst zwischen den Fassungen. Paletten ohne diesen Anker werden
 * übersprungen, damit eine abweichende Contao-Fassung keinen Fehler auslöst.
 */
$paletteManipulator = PaletteManipulator::create()
    ->addField('backendmenueHideGlobal', 'useCE', PaletteManipulator::POSITION_AFTER);

foreach (array_keys($GLOBALS['TL_DCA']['tl_user']['palettes']) as $palette) {
    if ('__selector__' === $palette || !\is_string($GLOBALS['TL_DCA']['tl_user']['palettes'][$palette])) {
        continue;
    }

    if (!str_contains($GLOBALS['TL_DCA']['tl_user']['palettes'][$palette], 'useCE')) {
        continue;
    }

    $paletteManipulator->applyToPalette($palette, 'tl_user');
}
