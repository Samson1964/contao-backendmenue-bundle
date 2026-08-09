<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\BackendMenueBundle\BackendMenueBundle;

/**
 * Registriert das BackendMenueBundle im Contao Manager.
 */
class Plugin implements BundlePluginInterface
{
    /**
     * Gibt die Bündel zurück, die registriert werden sollen.
     *
     * @param ParserInterface $parser Der Parser für Bundle-Konfigurationen
     *
     * @return BundleConfig[] Array of Bundle-Konfigurationen
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(BackendMenueBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
        ];
    }
}
