<?php

declare(strict_types=1);

/**
 * Registriere Backend-Module für die Verwaltung von Menübereichen und Zuordnungen.
 */
$GLOBALS['BE_MOD']['system']['backendmenue_bereiche'] = [
    'tables' => ['tl_backendmenue_bereiche', 'tl_backendmenue_zuordnungen'],
];
