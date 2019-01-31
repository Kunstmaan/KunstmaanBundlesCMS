<?php

namespace Kunstmaan\PagePartBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_pagepart.page_part_configuration_reader.class' => \Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReader::class,
            'kunstmaan_pagepart.page_part_configuration_parser.class' => \Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationParser::class,
            'kunstmaan_pagepart.page_template_configuration_reader.class' => \Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationReader::class,
            'kunstmaan_pagepart.page_template_configuration_parser.class' => \Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationParser::class,
            'kunstmaan_page_part.page_template.page_template_configuration_service.class' => \Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanPagePartBundle 5.2 and will be removed in KunstmaanPagePartBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
