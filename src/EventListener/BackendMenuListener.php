<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\EventListener;

use Contao\CoreBundle\Attributes\AsHook;
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;

/**
 * Listener für die Manipulation der Backend-Menüstruktur.
 *
 * Wird beim Laden des Backend-Moduls aufgerufen und manipuliert die globale
 * BE_MOD-Struktur entsprechend den konfigurierten Menübereichen.
 */
#[AsHook('loadBackendModule', priority: 32)]
class BackendMenuListener
{
    /**
     * Konstruktor mit Dependency Injection.
     *
     * @param BackendMenuManipulator $manipulator Der Service zur Menü-Manipulation
     */
    public function __construct(
        private readonly BackendMenuManipulator $manipulator,
    ) {
    }

    /**
     * Manipuliert das Backend-Menü beim Laden eines Backend-Moduls.
     *
     * @param string $module Der Name des zu ladenden Moduls
     *
     * @return void
     */
    public function __invoke(string $module): void
    {
        $this->manipulator->manipulateBackendMenu();
    }
}
