<?php

namespace Kunstmaan\CookieBundle\DependencyInjection;

use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_cookie');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_cookie');
        }

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
