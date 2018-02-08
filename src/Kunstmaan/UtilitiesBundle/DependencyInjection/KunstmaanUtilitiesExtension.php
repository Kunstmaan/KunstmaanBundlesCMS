<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Kunstmaan\UtilitiesBundle\Twig\UtilitiesTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanUtilitiesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_utilities.shell' => new Alias(Shell::class),
                'kunstmaan_utilities.cipher' => new Alias(UrlSafeCipher::class),
                'kunstmaan_utilities.slugifier' => new Alias(Slugifier::class),
                'kunstmaan_utilities.twig.extension' => new Alias(UtilitiesTwigExtension::class),
                SlugifierInterface::class => new Alias(Slugifier::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_utilities.slugifier.class', Slugifier::class, true],
                ['kunstmaan_utilities.shell.class', Shell::class, true],
                ['kunstmaan_utilities.cipher.class', UrlSafeCipher::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }
}
