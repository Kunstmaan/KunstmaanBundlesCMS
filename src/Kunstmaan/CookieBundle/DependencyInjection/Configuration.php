<?php

namespace Kunstmaan\CookieBundle\DependencyInjection;

use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_cookie');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('consent_cookie')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('lifetime')->defaultValue(LegalCookieHelper::DEFAULT_COOKIE_LIFETIME)->info('Default lifetime of 10 years')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
