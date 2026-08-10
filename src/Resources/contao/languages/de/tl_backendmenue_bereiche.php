<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['name'] = ['Name', 'Name des Menübereichs'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['position'] = ['Position', 'Position im Backend-Menü: 1 = ganz oben, 2 = an zweiter Stelle usw.; größere Werte hängen den Bereich ans Ende'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconType'] = ['Icon-Herkunft', 'Ein Icon aus den mitgelieferten Sammlungen wählen oder eine eigene Bilddatei verwenden'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['icon'] = ['Icon', 'Icon aus Font Awesome 6, Lucide oder dem Contao-Backend; wird vor dem Bereichsnamen angezeigt'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconFile'] = ['Icon-Datei', 'Eigene Bilddatei aus der Dateiverwaltung (SVG, PNG oder GIF). Am besten quadratisch und einfarbig, damit sie im Menü sauber wirkt'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconColor'] = ['Icon-Farbe', 'Optional. Bleibt das Feld leer, wird die Standardfarbe des Backend-Themes verwendet. Bei Bilddateien wird die Farbe auf die Silhouette des Bildes angewendet'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['edit'] = ['Module verwalten', 'Die Modul-Zuordnungen des Bereichs ID %s verwalten'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['editheader'] = ['Bereich bearbeiten', 'Die Einstellungen des Bereichs ID %s bearbeiten'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['delete'] = ['Löschen'];
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['show'] = ['Anzeigen'];

$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['iconTypes'] = [
    'library' => 'Aus einer Icon-Sammlung',
    'file' => 'Eigene Bilddatei',
];

$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['general_legend'] = 'Allgemein';
$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['icon_legend'] = 'Icon';

$GLOBALS['TL_LANG']['tl_backendmenue_bereiche']['usageHint'] = 'Ein Bereich erscheint erst dann im Backend-Menü, wenn ihm über „Module verwalten" mindestens ein Modul zugeordnet wurde.';
