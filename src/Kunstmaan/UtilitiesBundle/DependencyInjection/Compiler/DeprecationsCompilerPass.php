<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection\Compiler;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Twig\UtilitiesTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\UtilitiesBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_utilities.shell', Shell::class],
                ['kunstmaan_utilities.cipher', UrlSafeCipher::class],
                ['kunstmaan_utilities.slugifier', Slugifier::class],
                ['kunstmaan_utilities.twig.extension', UtilitiesTwigExtension::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_utilities.slugifier.class', Slugifier::class],
                ['kunstmaan_utilities.shell.class', Shell::class],
                ['kunstmaan_utilities.cipher.class', UrlSafeCipher::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanUtilitiesBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanUtilitiesBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}
