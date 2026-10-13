<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Schachbulle\BackendMenueBundle\Service\AreaAccess;
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

        $this->createManipulator()->manipulateBackendMenu();

        $this->assertFalse(isset($GLOBALS['BE_MOD']));
    }

    /**
     * Testet, dass das Menü unverändert bleibt, wenn keine Bereiche gelten.
     *
     * @test
     */
    public function testDoesNothingIfNoAreasApply(): void
    {
        $GLOBALS['BE_MOD'] = [
            'system' => [
                'settings' => ['tables' => ['tl_settings']],
            ],
        ];

        $original = $GLOBALS['BE_MOD'];

        $this->createManipulator()->manipulateBackendMenu();

        $this->assertSame($original, $GLOBALS['BE_MOD']);
    }

    /**
     * Testet, dass die Standardbereiche erhalten bleiben.
     *
     * @test
     */
    public function testDefaultAreasArePreserved(): void
    {
        $GLOBALS['BE_MOD'] = [
            'content' => ['page' => ['tables' => ['tl_page']]],
            'system' => ['log' => ['tables' => ['tl_log']]],
        ];

        $this->createManipulator()->manipulateBackendMenu();

        $this->assertArrayHasKey('content', $GLOBALS['BE_MOD']);
        $this->assertArrayHasKey('system', $GLOBALS['BE_MOD']);
    }

    /**
     * Testet, dass noch keine Bereiche angewendet wurden.
     *
     * @test
     */
    public function testNoAppliedAreasWithoutData(): void
    {
        $GLOBALS['BE_MOD'] = ['system' => ['log' => ['tables' => ['tl_log']]]];

        $manipulator = $this->createManipulator();
        $manipulator->manipulateBackendMenu();

        $this->assertSame([], $manipulator->getAppliedAreas());
    }

    /**
     * Baut einen Manipulator mit einem Zugriffs-Doppel ohne geltende Bereiche.
     *
     * @return BackendMenuManipulator Der vorbereitete Service
     */
    private function createManipulator(): BackendMenuManipulator
    {
        $access = $this->createMock(AreaAccess::class);
        $access->method('getAreasForMenu')->willReturn([]);

        return new BackendMenuManipulator($access);
    }
}
