<?php

declare(strict_types=1);

/**
 * Registriere Backend-Module für die Verwaltung von Menübereichen und Zuordnungen.
 */
$GLOBALS['BE_MOD']['system']['backendmenue_bereiche'] = [
    'tables' => ['tl_backendmenue_bereiche', 'tl_backendmenue_zuordnungen'],
];

/**
 * Hook zum Laden der DCA-Dateien.
 */
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = ['Schachbulle\BackendMenueBundle\EventListener\LoadDataContainerListener', '__invoke'];
