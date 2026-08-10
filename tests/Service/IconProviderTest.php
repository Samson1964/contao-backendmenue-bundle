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
     * Testet, dass Font-Awesome-Icons zurückgegeben werden.
     *
     * @test
     */
    public function testGetFontAwesomeIcons(): void
    {
        $provider = new IconProvider();
        $icons = $provider->getFontAwesomeIcons();

        $this->assertNotEmpty($icons);
        $this->assertArrayHasKey('fa-star', $icons);
        $this->assertArrayHasKey('fa-chess', $icons);
        $this->assertArrayHasKey('fa-wrench', $icons);
    }

    /**
     * Testet, dass Contao-Standard-Icons zurückgegeben werden.
     *
     * @test
     */
    public function testGetContaoStandardIcons(): void
    {
        $provider = new IconProvider();
        $icons = $provider->getContaoStandardIcons();

        $this->assertNotEmpty($icons);
        $this->assertArrayHasKey('settings.svg', $icons);
        $this->assertArrayHasKey('article.svg', $icons);
    }

    /**
     * Testet, dass Lucide-Icons zurückgegeben werden.
     *
     * @test
     */
    public function testGetLucideIcons(): void
    {
        $provider = new IconProvider();
        $icons = $provider->getLucideIcons();

        $this->assertNotEmpty($icons);
        $this->assertArrayHasKey('lucide-crown', $icons);
        $this->assertArrayHasKey('lucide-settings', $icons);
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

        $this->assertCount(
            \count($provider->getFontAwesomeIcons())
                + \count($provider->getLucideIcons())
                + \count($provider->getContaoStandardIcons()),
            $allIcons
        );
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

        $this->assertArrayHasKey('Font Awesome 6', $iconsForDca);
        $this->assertArrayHasKey('Lucide', $iconsForDca);
        $this->assertArrayHasKey('Contao Standard', $iconsForDca);
    }

    /**
     * Testet die Codepoint-Auflösung für Font-Awesome-Icons inklusive FA5-Aliassen.
     *
     * @test
     */
    public function testGetFaCodepoint(): void
    {
        $provider = new IconProvider();

        $this->assertSame('f005', $provider->getFaCodepoint('fa-star'));
        $this->assertSame('f439', $provider->getFaCodepoint('fa-chess'));

        // FA5-Alt-Namen müssen auf den FA6-Codepoint aufgelöst werden
        $this->assertSame('f013', $provider->getFaCodepoint('fa-cog'));
        $this->assertSame('f002', $provider->getFaCodepoint('fa-search'));

        $this->assertNull($provider->getFaCodepoint('settings.svg'));
        $this->assertNull($provider->getFaCodepoint('fa-unbekannt'));
    }

    /**
     * Testet die Codepoint-Auflösung für Lucide-Icons.
     *
     * @test
     */
    public function testGetLucideCodepoint(): void
    {
        $provider = new IconProvider();

        $this->assertSame('e1d6', $provider->getLucideCodepoint('lucide-crown'));
        $this->assertSame('e154', $provider->getLucideCodepoint('lucide-settings'));
        $this->assertNull($provider->getLucideCodepoint('fa-star'));
        $this->assertNull($provider->getLucideCodepoint('lucide-unbekannt'));
    }

    /**
     * Testet die Pfad-Auflösung für Contao-Standard-Icons.
     *
     * @test
     */
    public function testGetContaoIconPath(): void
    {
        $provider = new IconProvider();

        $this->assertSame('system/themes/flexible/icons/settings.svg', $provider->getContaoIconPath('settings.svg'));
        $this->assertNull($provider->getContaoIconPath('fa-star'));
        $this->assertNull($provider->getContaoIconPath('../../../etc/passwd'));
    }

    /**
     * Testet die Validierung von Icons.
     *
     * @test
     */
    public function testValidateIcon(): void
    {
        $provider = new IconProvider();

        $this->assertSame('fa-star', $provider->validateIcon('fa-star'));
        $this->assertSame('settings.svg', $provider->validateIcon('settings.svg'));
        $this->assertSame('', $provider->validateIcon('invalid-icon'));
    }
}
