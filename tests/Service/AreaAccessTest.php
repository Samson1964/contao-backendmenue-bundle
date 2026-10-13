<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Tests\Service;

use Contao\BackendUser;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use PHPUnit\Framework\TestCase;
use Schachbulle\BackendMenueBundle\Service\AreaAccess;

/**
 * Tests für die Zugriffs- und Geltungslogik der Menübereiche.
 *
 * Die Datenbankabfrage wird über eine Testklasse ersetzt, die loadAreas()
 * überschreibt; geprüft wird die Auswahl- und Rechtelogik darüber.
 */
class AreaAccessTest extends TestCase
{
    /**
     * Testet, dass ein Administrator alle Bereiche verwalten darf.
     *
     * @test
     */
    public function testAdminCanEditAllAreas(): void
    {
        $access = $this->createAccess(['id' => 7, 'isAdmin' => true]);

        $this->assertSame([1, 2, 3], $access->getEditableAreaIds());
        $this->assertTrue($access->canEditArea(2));
        $this->assertTrue($access->canEditArea(3));
    }

    /**
     * Testet, dass ein normaler Benutzer nur seine eigenen Bereiche verwalten darf.
     *
     * Globale Bereiche sind für ihn tabu, auch wenn sie in seinem Menü erscheinen.
     *
     * @test
     */
    public function testUserCanEditOnlyOwnAreas(): void
    {
        $access = $this->createAccess(['id' => 7, 'isAdmin' => false]);

        $this->assertSame([2], $access->getEditableAreaIds());
        $this->assertTrue($access->canEditArea(2));
        $this->assertFalse($access->canEditArea(1), 'Globaler Bereich darf nicht bearbeitbar sein');
        $this->assertFalse($access->canEditArea(3), 'Fremder Bereich darf nicht bearbeitbar sein');
    }

    /**
     * Testet, dass im Menü die globalen und die eigenen Bereiche erscheinen.
     *
     * @test
     */
    public function testMenuShowsGlobalAndOwnAreas(): void
    {
        $access = $this->createAccess(['id' => 7, 'isAdmin' => false]);

        $this->assertSame([1, 2], array_keys($access->getAreasForMenu()));
    }

    /**
     * Testet, dass abgeschaltete globale Bereiche aus dem Menü verschwinden.
     *
     * @test
     */
    public function testHiddenGlobalAreasAreExcludedFromMenu(): void
    {
        $access = $this->createAccess(['id' => 7, 'isAdmin' => false, 'backendmenueHideGlobal' => '1']);

        $this->assertTrue($access->hidesGlobalAreas());
        $this->assertSame([2], array_keys($access->getAreasForMenu()), 'Nur der eigene Bereich bleibt');
    }

    /**
     * Testet, dass auch ein Administrator nur seine eigenen und die globalen
     * Bereiche im Menü sieht — fremde persönliche Bereiche gehören nicht dazu.
     *
     * @test
     */
    public function testAdminMenuExcludesForeignPersonalAreas(): void
    {
        $access = $this->createAccess(['id' => 7, 'isAdmin' => true]);

        $this->assertSame([1, 2], array_keys($access->getAreasForMenu()));
    }

    /**
     * Testet, dass ohne angemeldeten Benutzer nichts geliefert wird.
     *
     * Das ist der Fall auf der Kommandozeile, etwa beim Aufwärmen des Caches.
     *
     * @test
     */
    public function testNoUserMeansNoAreas(): void
    {
        $access = $this->createAccess(null);

        $this->assertFalse($access->isAdmin());
        $this->assertSame(0, $access->getUserId());
        $this->assertSame([], $access->getAreasForMenu());
        $this->assertSame([], $access->getEditableAreaIds());
    }

    /**
     * Testet die Sichtbarkeitsregeln für Backend-Module.
     *
     * @test
     */
    public function testModuleVisibility(): void
    {
        $access = $this->createAccess(['id' => 7, 'isAdmin' => false, 'modules' => ['faq']]);

        $this->assertTrue($access->isModuleVisible('faq', []));
        $this->assertFalse($access->isModuleVisible('news', []), 'Ohne Freigabe nicht sichtbar');
        $this->assertFalse(
            $access->isModuleVisible('faq', ['hideInNavigation' => true]),
            'Versteckte Module erscheinen nie'
        );
        $this->assertTrue(
            $access->isModuleVisible('news', ['disablePermissionChecks' => true]),
            'Module ohne Rechteprüfung erscheinen immer'
        );
    }

    /**
     * Baut einen AreaAccess mit festem Bereichsbestand und einem Benutzer-Doppel.
     *
     * Der Bestand enthält einen globalen Bereich (ID 1), einen persönlichen des
     * Testbenutzers 7 (ID 2) und einen persönlichen eines anderen (ID 3).
     *
     * @param array|null $userData Eigenschaften des angemeldeten Benutzers,
     *                             oder null wenn niemand angemeldet ist
     *
     * @return AreaAccess Der vorbereitete Service
     */
    private function createAccess(array|null $userData): AreaAccess
    {
        $user = null;

        if (null !== $userData) {
            // Nur hasAccess() ersetzen: Contao-Benutzer halten ihre Werte über
            // __get/__set in einem internen Array. Ein vollständiger Mock würde
            // diese Magie ebenfalls ersetzen und jede Zuweisung verschlucken.
            $user = $this
                ->getMockBuilder(BackendUser::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['hasAccess'])
                ->getMock()
            ;

            $user->id = $userData['id'];
            // isAdmin ist in Contao ein berechnetes Attribut, hinterlegt wird
            // die Datenbankspalte "admin"
            $user->admin = $userData['isAdmin'] ? '1' : '';
            $user->backendmenueHideGlobal = $userData['backendmenueHideGlobal'] ?? '';

            $modules = $userData['modules'] ?? [];

            $user
                ->method('hasAccess')
                ->willReturnCallback(
                    static fn ($field, $array): bool => 'modules' === $array && \in_array($field, $modules, true)
                )
            ;
        }

        $adapter = $this->createMock(Adapter::class);
        $adapter->method('__call')->willReturn($user);

        $framework = $this->createMock(ContaoFramework::class);
        $framework->method('getAdapter')->willReturn($adapter);

        return new class($framework) extends AreaAccess {
            /**
             * Liefert einen festen Bereichsbestand statt der Datenbankabfrage.
             *
             * @return array Die Testbereiche
             */
            protected function loadAreas(): array
            {
                return [
                    1 => $this->area('Global', 'global', []),
                    2 => $this->area('Meiner', 'user', [7]),
                    3 => $this->area('Fremder', 'user', [8]),
                ];
            }

            /**
             * Baut einen Bereichs-Datensatz für den Test.
             *
             * @param string $name  Der Name des Bereichs
             * @param string $scope 'global' oder 'user'
             * @param array  $users Die zugeordneten Benutzer-IDs
             *
             * @return array Der Datensatz in der Form, die loadAreas() liefert
             */
            private function area(string $name, string $scope, array $users): array
            {
                return [
                    'name' => $name,
                    'iconType' => 'library',
                    'icon' => 'fa-star',
                    'iconFile' => null,
                    'iconColor' => '',
                    'position' => 1,
                    'scope' => $scope,
                    'users' => $users,
                ];
            }
        };
    }
}
