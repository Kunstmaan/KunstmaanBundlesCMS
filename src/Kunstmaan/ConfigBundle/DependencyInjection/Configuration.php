<?php

namespace Kunstmaan\ConfigBundle\DependencyInjection;

use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface as KunstmaanConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_config');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('entities')
                    ->defaultValue([])
                    ->normalizeKeys(false)
                    ->info('The list of entities to manage in the settings zone')
                    ->prototype('scalar')->end()
                    ->beforeNormalization()
                        ->always()
                        ->then(function ($entities) {
                            foreach ($entities as $entity) {
                                if (!class_exists($entity)) {
                                    throw new \InvalidArgumentException(sprintf('Entity "%s" does not exist', $entity));
                                }

                                // Check if entity implements the ConfigurationInterface.
                                if (!\in_array(KunstmaanConfigurationInterface::class, class_implements($entity))) {
                                    throw new \RuntimeException(sprintf('The entity class "%s" needs to implement the %s', $entity, KunstmaanConfigurationInterface::class));
                                }
                            }

                            return $entities;
                        })
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
