<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_admin');

        $rootNode
            ->fixXmlConfig('admin_locale')
            ->fixXmlConfig('menu_item')
            ->children()
                ->scalarNode('admin_password')->end()
                ->scalarNode('dashboard_route')->end()
                ->arrayNode('admin_locales')
                    ->defaultValue(array('en'))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('session_security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('ip_check')->defaultFalse()->end()
                        ->booleanNode('user_agent_check')->defaultFalse()->end()
                    ->end()
                ->end()
                ->scalarNode('default_admin_locale')->cannotBeEmpty()->defaultValue('en')->end()
                ->booleanNode('enable_console_exception_listener')->defaultTrue()->end()
                ->arrayNode('menu_items')
                    ->defaultValue([])
                    ->useAttributeAsKey('route', false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode('route')->isRequired()->end()
                            ->scalarNode('label')->isRequired()->end()
                            ->scalarNode('role')->defaultNull()->end()
                            ->arrayNode('params')->defaultValue([])->prototype('scalar')->end()->end()
                            ->scalarNode('parent')->defaultValue('KunstmaanAdminBundle_modules')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('google_signin')
                    ->addDefaultsIfNotSet()
                    ->canBeEnabled()
                    ->beforeNormalization()
                        ->always()
                        ->then(function ($v) {
                            if ($v === true || (isset($v['enabled']) && $v['enabled'])) {
                                if (empty($v['client_id']) || empty($v['client_secret'])) {
                                    throw new InvalidConfigurationException('The "client_id" and "client_secret" settings are required under the "google_signin" group.');
                                }
                            } else {
                                unset($v['client_id'], $v['client_secret'], $v['hosted_domains']);
                            }

                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('client_id')->defaultNull()->end()
                        ->scalarNode('client_secret')->defaultNull()->end()
                        ->arrayNode('hosted_domains')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('domain_name')->isRequired()->end()
                                    ->arrayNode('access_levels')
                                        ->isRequired()
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
