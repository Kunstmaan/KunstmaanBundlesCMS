<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_admin');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('admin_locale')
            ->fixXmlConfig('menu_item')
            ->children()
                ->scalarNode('website_title')->defaultNull()->end()
                ->booleanNode('multi_language')->isRequired()->defaultFalse()->end()
                ->scalarNode('required_locales')->isRequired()->end()
                ->scalarNode('default_locale')->isRequired()->end()
                ->arrayNode('authentication')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('user_class')->defaultValue(User::class)->end()
                        ->scalarNode('group_class')->defaultValue(Group::class)->end()
                        ->arrayNode('mailer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue(SymfonyMailerService::class)->end()
                                ->scalarNode('from_address')->defaultValue('kunstmaancms@myproject.dev')->end()
                                ->scalarNode('from_name')->defaultValue('Kunstmaan CMS')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                ->scalarNode('default_admin_locale')->cannotBeEmpty()->defaultValue('en')->end()
                ->booleanNode('enable_console_exception_listener')->defaultTrue()->end()
                ->booleanNode('enable_toolbar_helper')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('exception_logging')
                    ->treatFalseLike(['enabled' => false])
                    ->treatTrueLike(['enabled' => true])
                    ->treatNullLike(['enabled' => true])
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->arrayNode('exclude_patterns')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
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
                ->arrayNode('password_restrictions')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('min_digits')->defaultNull()->end()
                        ->integerNode('min_uppercase')->defaultNull()->end()
                        ->integerNode('min_special_characters')->defaultNull()->end()
                        ->integerNode('min_length')->defaultNull()->end()
                        ->integerNode('max_length')->defaultNull()->end()
                    ->end()
                ->end()
                ->booleanNode('hide_sidebar')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
