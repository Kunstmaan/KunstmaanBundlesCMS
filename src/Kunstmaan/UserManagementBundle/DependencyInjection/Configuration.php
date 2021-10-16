<?php

namespace Kunstmaan\UserManagementBundle\DependencyInjection;

use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_user_management');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('user')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adminlist_configurator')->defaultValue(UserAdminListConfigurator::class)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
