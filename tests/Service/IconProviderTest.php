<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Schachbulle\BackendMenueBundle\Service\IconProvider;

/**
 * Tests für den IconProvider Service.
 */
class IconProviderTest extends TestCase
{
    /**
     * Testet, dass Font Awesome Icons zurückgegeben werden.
     *
     * @test
     */
    public function testGetFontAwesomeIcons(): void
    {
        $provider = new IconProvider();
        $icons = $provider->getFontAwesomeIcons();

        $this->assertIsArray($icons);
        $this->assertNotEmpty($icons);
        $this->assertArrayHasKey('fa-star', $icons);
        $this->assertArrayHasKey('fa-tools', $icons);
    }

    /**
     * Testet, dass Contao Standard Icons zurückgegeben werden.
     *
     * @test
     */
    public function testGetContaoStandardIcons(): void
    {
        $provider = new IconProvider();
        $icons = $provider->getContaoStandardIcons();

        $this->assertIsArray($icons);
        $this->assertNotEmpty($icons);
        $this->assertArrayHasKey('settings.svg', $icons);
        $this->assertArrayHasKey('page.svg', $icons);
    }

    /**
     * Testet, dass alle Icons kombiniert zurückgegeben werden.
     *
     * @test
     */
    public function testGetAllIcons(): void
    {
        $provider = new IconProvider();
        $allIcons = $provider->getAllIcons();
        $fontAwesomeIcons = $provider->getFontAwesomeIcons();
        $contaoIcons = $provider->getContaoStandardIcons();

        $this->assertIsArray($allIcons);
        $this->assertCount(\count($fontAwesomeIcons) + \count($contaoIcons), $allIcons);
    }

    /**
     * Testet, dass Icons für die DCA mit Gruppierung zurückgegeben werden.
     *
     * @test
     */
    public function testGetIconsForDca(): void
    {
        $provider = new IconProvider();
        $iconsForDca = $provider->getIconsForDca();

        $this->assertIsArray($iconsForDca);
        $this->assertArrayHasKey('Font Awesome 6', $iconsForDca);
        $this->assertArrayHasKey('Contao Standard', $iconsForDca);
    }

    /**
     * Testet die Validierung von Icons.
     *
     * @test
     */
    public function testValidateIcon(): void
    {
        $provider = new IconProvider();

        $this->assertEquals('fa-star', $provider->validateIcon('fa-star'));
        $this->assertEquals('settings.svg', $provider->validateIcon('settings.svg'));
        $this->assertEquals('', $provider->validateIcon('invalid-icon'));
    }
}
