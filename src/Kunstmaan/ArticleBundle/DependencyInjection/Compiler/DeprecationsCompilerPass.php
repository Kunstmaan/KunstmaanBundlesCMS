<?php

namespace Kunstmaan\ArticleBundle\DependencyInjection\Compiler;

use Kunstmaan\ArticleBundle\Twig\ArticleTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\ArticleBundle\DependencyInjection\Compiler
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
                ['kunstmaan_articlebundle.twig.extension', ArticleTwigExtension::class],
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations)
    {
        foreach ($deprecations as $deprecation) {
            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }
            $definition->setClass($deprecation[1]);
            $definition->setDeprecated(
                true,
                'Passing a "%service_id%" instance is deprecated since KunstmaanArticleBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
            );
            $container->setDefinition($deprecation[0], $definition);
        }
    }
}
