<?php

namespace Kunstmaan\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_form');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('deletable_formsubmissions')->defaultFalse()->end()
                ->arrayNode('file_upload')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('allowed_extensions')
                            ->info('Allow-list of file extensions that can be uploaded through the form page file field.')
                            ->scalarPrototype()->end()
                            ->defaultValue([
                                'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'txt', 'csv', 'rtf', 'zip',
                                'jpg', 'jpeg', 'png', 'gif', 'webp',
                                'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'jxl'
                            ])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
