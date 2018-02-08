<?php

namespace Kunstmaan\PagePartBundle\DependencyInjection;

use Kunstmaan\PagePartBundle\EventListener\CloneListener;
use Kunstmaan\PagePartBundle\EventListener\NodeListener;
use Kunstmaan\PagePartBundle\Helper\Services\PagePartCreatorService;
use Kunstmaan\PagePartBundle\PagePartAdmin\Builder;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationParser;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationParser;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationReader;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Kunstmaan\PagePartBundle\Repository\PageTemplateConfigurationRepository;
use Kunstmaan\PagePartBundle\Twig\Extension\PagePartAdminTwigExtension;
use Kunstmaan\PagePartBundle\Twig\Extension\PagePartTwigExtension;
use Kunstmaan\PagePartBundle\Twig\Extension\PageTemplateTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanPagePartExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('kunstmaan_page_part.extended', $configs['extended_pagepart_chooser']);
        $container->setParameter('kunstmaan_page_part.page_parts_presets', $configs['pageparts']);
        $container->setParameter('kunstmaan_page_part.page_templates_presets', $configs['pagetemplates']);

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_pagepart.pageparts' => Builder::class,
                'kunstmaan_page_part.page_part_configuration_reader' => new Alias(PagePartConfigurationReader::class),
                'kunstmaan_page_part.page_part_configuration_parser' => new Alias(PagePartConfigurationParser::class),
                'kunstmaan_page_part.page_template_configuration_reader' => new Alias(PageTemplateConfigurationReader::class),
                'kunstmaan_page_part.page_template_configuration_parser' => new Alias(PageTemplateConfigurationParser::class),
                'kunstmaan_page_part.page_template.page_template_configuration_service' => new Alias(PageTemplateConfigurationService::class),
                'kunstmaan_page_part.repository.page_template_configuration' => new Alias(PageTemplateConfigurationRepository::class, false),
                'kunstmaan_pagepartadmin.factory' => PagePartAdminFactory::class,
                'kunstmaan_pagepartadmin.twig.extension' => PagePartAdminTwigExtension::class,
                'kunstmaan_pageparts.twig.extension' => PagePartTwigExtension::class,
                'kunstmaan_pagetemplate.twig.extension' => PageTemplateTwigExtension::class,
                'kunstmaan_pageparts.pagepart_creator_service' => PagePartCreatorService::class,
                'kunstmaan_pageparts.edit_node.listener' => NodeListener::class,
                'kunstmaan_pageparts.clone.listener' => CloneListener::class,
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_pagepart.page_part_configuration_reader.class', PagePartConfigurationReader::class, true],
                ['kunstmaan_pagepart.page_part_configuration_parser.class', PagePartConfigurationParser::class, false],
                ['kunstmaan_pagepart.page_template_configuration_reader.class', PageTemplateConfigurationReader::class, true],
                ['kunstmaan_pagepart.page_template_configuration_parser.class', PageTemplateConfigurationParser::class, false],
                ['kunstmaan_page_part.page_template.page_template_configuration_service.class', PageTemplateConfigurationService::class, true],
                ['kunstmaan_page_part.admin_factory.class', PagePartAdminFactory::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }
}
