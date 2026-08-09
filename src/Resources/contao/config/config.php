<?php

declare(strict_types=1);

/**
 * Registriere Backend-Module für die Verwaltung von Menübereichen und Zuordnungen.
 */
$GLOBALS['BE_MOD']['system']['backendmenue_bereiche'] = [
    'tables' => ['tl_backendmenue_bereiche', 'tl_backendmenue_zuordnungen'],
];

/**
 * Registriere die Datentabellen für die Datenbank-Verwaltung.
 */
$GLOBALS['TL_DCA']['tl_backendmenue_bereiche']['config']['closed'] = false;
$GLOBALS['TL_DCA']['tl_backendmenue_zuordnungen']['config']['closed'] = false;
