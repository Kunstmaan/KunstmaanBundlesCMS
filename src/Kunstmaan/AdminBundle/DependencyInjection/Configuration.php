<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_admin');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_admin');
        }

        $rootNode
            ->fixXmlConfig('admin_locale')
            ->fixXmlConfig('menu_item')
            ->children()
                ->scalarNode('website_title')->defaultNull()->end()
                ->scalarNode('multi_language') //NEXT_MAJOR: change type to booleanNode and make required or provide default value
                    ->defaultNull()
                    ->beforeNormalization()->ifString()->then(function ($v) {
                        // Workaroud to allow detecting if value is not provided. Can be removed when type is switched to booleanNode
                        return (bool) $v;
                    })->end()
                ->end()
                ->scalarNode('required_locales')->defaultNull()->end() //NEXT_MAJOR: make config required
                ->scalarNode('default_locale')->defaultNull()->end() //NEXT_MAJOR: make config required
                ->scalarNode('admin_password')->end()
                ->scalarNode('dashboard_route')->end()
                ->scalarNode('admin_prefix')->defaultValue('admin')->end()
                ->arrayNode('admin_locales')
                    ->defaultValue(['en'])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('session_security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('ip_check')->defaultFalse()->end()
                        ->booleanNode('user_agent_check')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('admin_exception_excludes')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('default_admin_locale')->cannotBeEmpty()->defaultValue('en')->end()
                ->booleanNode('enable_console_exception_listener')->defaultTrue()->end()
                ->booleanNode('enable_toolbar_helper')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('provider_keys')
                    ->prototype('array')->end()
                    ->setDeprecated('The "%provider_keys%" is deprecated. Use "toolbar_firewall_names" instead')
                ->end()
                ->arrayNode('toolbar_firewall_names')
                    ->defaultValue(['main'])
                    ->prototype('array')->end()
                ->end()
                ->scalarNode('admin_firewall_name')
                    ->defaultValue('main')
                ->end()
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
                ->arrayNode('password_restrictions')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('min_digits')->defaultNull()->end()
                        ->integerNode('min_uppercase')->defaultNull()->end()
                        ->integerNode('min_special_characters')->defaultNull()->end()
                        ->integerNode('min_length')->defaultNull()->end()
                        ->integerNode('max_length')->defaultNull()->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}
