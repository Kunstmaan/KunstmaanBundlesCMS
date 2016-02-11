<?php

namespace Kunstmaan\LanguageChooserBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('kunstmaan_language_chooser');

        $rootNode
            ->children()
                ->arrayNode('languagechooserlocales')->defaultValue(array('en'))
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('autodetectlanguage')->defaultValue(true)->end()
                ->scalarNode('showlanguagechooser')->defaultValue(true)->end()
                ->scalarNode('languagechoosertemplate')->defaultValue('KunstmaanLanguageChooserBundle:Default:language-chooser.html.twig')->end()
            ->end();

        return $treeBuilder;
    }
}
