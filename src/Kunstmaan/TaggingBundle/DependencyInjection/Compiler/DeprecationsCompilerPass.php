<?php

namespace Kunstmaan\TaggingBundle\DependencyInjection\Compiler;

use Kunstmaan\TaggingBundle\Entity\TagManager;
use Kunstmaan\TaggingBundle\EventListener\CloneListener;
use Kunstmaan\TaggingBundle\EventListener\IndexNodeEventListener;
use Kunstmaan\TaggingBundle\EventListener\TagsListener;
use Kunstmaan\TaggingBundle\Form\TagsAdminType;
use Kunstmaan\TaggingBundle\Helper\Menu\TagMenuAdaptor;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\TaggingBundle\DependencyInjection\Compiler
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
                ['kuma_tagging.tag_manager', TagManager::class],
                ['kuma_tagging.listener', TagsListener::class],
                ['kuma_tagging.clone.listener', CloneListener::class],
                ['kuma_tagging.index_node.listener', IndexNodeEventListener::class],
                ['kuma_tagging.menu.adaptor', TagMenuAdaptor::class],
                ['form.type.tags', TagsAdminType::class],
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
                'Passing a "%service_id%" instance is deprecated since KunstmaanTaggingBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
            );
            $container->setDefinition($deprecation[0], $definition);
        }
    }
}
