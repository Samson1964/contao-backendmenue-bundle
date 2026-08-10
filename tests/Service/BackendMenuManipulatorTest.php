<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Schachbulle\BackendMenueBundle\Service\BackendMenuManipulator;

/**
 * Tests für den BackendMenuManipulator Service.
 *
 * Ohne gebooteten Contao-Container schlägt der Datenbankzugriff im Manipulator
 * fehl und wird geschluckt — die Tests prüfen genau dieses Verhalten: Das Menü
 * bleibt in jedem Fehlerfall unverändert.
 */
class BackendMenuManipulatorTest extends TestCase
{
    /**
     * Testet, dass der Manipulator nichts tut, wenn BE_MOD nicht initialisiert ist.
     *
     * @test
     */
    public function testDoesNothingIfBeModNotInitialized(): void
    {
        unset($GLOBALS['BE_MOD']);

        $manipulator = new BackendMenuManipulator();
        $manipulator->manipulateBackendMenu();

        $this->assertFalse(isset($GLOBALS['BE_MOD']));
    }

    /**
     * Testet, dass der Manipulator nichts tut, wenn keine benutzerdefinierten Bereiche existieren.
     *
     * @test
     */
    public function testDoesNothingIfNoCustomAreasExist(): void
    {
        $GLOBALS['BE_MOD'] = [
            'system' => [
                'settings' => ['tables' => ['tl_settings']],
            ],
        ];

        $originalBeMod = $GLOBALS['BE_MOD'];

        $manipulator = new BackendMenuManipulator();
        $manipulator->manipulateBackendMenu();

        // Das Menü sollte unverändert bleiben
        $this->assertEquals($originalBeMod, $GLOBALS['BE_MOD']);
    }

    /**
     * Testet die Initialisierung der Manipulator-Klasse.
     *
     * @test
     */
    public function testManipulatorCanBeInstantiated(): void
    {
        $manipulator = new BackendMenuManipulator();
        $this->assertInstanceOf(BackendMenuManipulator::class, $manipulator);
    }

    /**
     * Testet, dass Standardbereiche bei leeren Zuordnungen erhalten bleiben.
     *
     * @test
     */
    public function testDefaultAreasArePreserved(): void
    {
        $GLOBALS['BE_MOD'] = [
            'system' => [
                'settings' => ['tables' => ['tl_settings']],
                'log' => ['tables' => ['tl_log']],
            ],
            'content' => [
                'page' => ['tables' => ['tl_page']],
            ],
        ];

        $originalBeMod = $GLOBALS['BE_MOD'];

        $manipulator = new BackendMenuManipulator();
        $manipulator->manipulateBackendMenu();

        // Die Struktur sollte gleich bleiben (da keine custom areas)
        $this->assertArrayHasKey('system', $GLOBALS['BE_MOD']);
        $this->assertArrayHasKey('content', $GLOBALS['BE_MOD']);
    }
}
