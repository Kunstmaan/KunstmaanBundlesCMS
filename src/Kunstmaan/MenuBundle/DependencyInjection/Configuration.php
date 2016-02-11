<?php

namespace Kunstmaan\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_menu');

        $rootNode
            ->children()
                ->arrayNode('menus')
                    ->defaultValue(array())
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
