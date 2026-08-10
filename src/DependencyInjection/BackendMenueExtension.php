<?php

declare(strict_types=1);

namespace Schachbulle\BackendMenueBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Lädt die Service-Konfiguration des Bundles in den Symfony-Container.
 *
 * Ohne diese Extension-Klasse wird die services.yaml niemals eingelesen —
 * dann existiert kein einziger Service des Bundles und sämtliche Hooks
 * und DCA-Callbacks bleiben stumm. Symfony findet die Klasse über die
 * Namenskonvention <BundleName>Extension im DependencyInjection-Namespace.
 *
 * Die Basisklasse stammt bewusst aus der HttpKernel-Komponente: Die Variante
 * aus der DependencyInjection-Komponente gibt es erst ab Symfony 6.1 und
 * stünde unter Contao 4.13 (Symfony 5.4) nicht zur Verfügung.
 */
class BackendMenueExtension extends Extension
{
    /**
     * Lädt die services.yaml des Bundles.
     *
     * @param array            $configs   Die Konfigurationswerte (hier ungenutzt,
     *                                    das Bundle hat keine eigene Konfiguration)
     * @param ContainerBuilder $container Der Symfony-Container im Aufbau
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
    }
}
