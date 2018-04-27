<?php

namespace Kunstmaan\PagePartBundle\DependencyInjection\Compiler;

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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\PagePartBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_pagepart.pageparts', Builder::class],
                ['kunstmaan_page_part.page_part_configuration_reader', PagePartConfigurationReader::class],
                ['kunstmaan_page_part.page_part_configuration_parser', PagePartConfigurationParser::class],
                ['kunstmaan_page_part.page_template_configuration_reader', PageTemplateConfigurationReader::class],
                ['kunstmaan_page_part.page_template_configuration_parser', PageTemplateConfigurationParser::class],
                ['kunstmaan_page_part.page_template.page_template_configuration_service', PageTemplateConfigurationService::class],
                ['kunstmaan_page_part.repository.page_template_configuration', PageTemplateConfigurationRepository::class, false],
                ['kunstmaan_pagepartadmin.factory', PagePartAdminFactory::class],
                ['kunstmaan_pagepartadmin.twig.extension', PagePartAdminTwigExtension::class],
                ['kunstmaan_pageparts.twig.extension', PagePartTwigExtension::class],
                ['kunstmaan_pagetemplate.twig.extension', PageTemplateTwigExtension::class],
                ['kunstmaan_pageparts.pagepart_creator_service', PagePartCreatorService::class],
                ['kunstmaan_pageparts.edit_node.listener', NodeListener::class],
                ['kunstmaan_pageparts.clone.listener', CloneListener::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_pagepart.page_part_configuration_reader.class', PagePartConfigurationReader::class],
                ['kunstmaan_pagepart.page_part_configuration_parser.class', PagePartConfigurationParser::class, false],
                ['kunstmaan_pagepart.page_template_configuration_reader.class', PageTemplateConfigurationReader::class],
                ['kunstmaan_pagepart.page_template_configuration_parser.class', PageTemplateConfigurationParser::class, false],
                ['kunstmaan_page_part.page_template.page_template_configuration_service.class', PageTemplateConfigurationService::class],
                ['kunstmaan_page_part.admin_factory.class', PagePartAdminFactory::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanPagePartBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanPagePartBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}
