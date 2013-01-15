<?php

namespace Kunstmaan\NodeBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('kunstmaan_node');

        //The version_timeout parameter sets the time, in seconds, when a new version
        //needs to be created when one saves a node and the timeout is passed.
        //e.g. timeout is 3600 seconds, so when a page is last saved more then 3600 seconds ago
        //a new version will be created.
        $rootNode->children()
            ->variableNode('version_timeout')
            ->defaultValue(3600)
            ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
