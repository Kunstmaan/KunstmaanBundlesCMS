<?php

namespace Kunstmaan\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_menu');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_menu');
        }

        $rootNode
            ->children()
                ->arrayNode('menus')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('menu_entity')->defaultValue('Kunstmaan\MenuBundle\Entity\Menu')->end()
                ->scalarNode('menuitem_entity')->defaultValue('Kunstmaan\MenuBundle\Entity\MenuItem')->end()
                ->scalarNode('menu_adminlist')->defaultValue('Kunstmaan\MenuBundle\AdminList\MenuAdminListConfigurator')->end()
                ->scalarNode('menuitem_adminlist')->defaultValue('Kunstmaan\MenuBundle\AdminList\MenuItemAdminListConfigurator')->end()
                ->scalarNode('menu_form')->defaultValue('Kunstmaan\MenuBundle\Form\MenuAdminType')->end()
                ->scalarNode('menuitem_form')->defaultValue('Kunstmaan\MenuBundle\Form\MenuItemAdminType')->end()
            ->end();

        return $treeBuilder;
    }
}
