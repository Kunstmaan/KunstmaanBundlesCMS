<?php

namespace Kunstmaan\RedirectBundle\DependencyInjection;

use Kunstmaan\RedirectBundle\Entity\Redirect;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_redirect');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('redirect_entity')->defaultValue(Redirect::class)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
