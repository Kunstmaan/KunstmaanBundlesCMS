<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection\Compiler;

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
            'kunstmaan_translator.menu.adaptor.class' => \Kunstmaan\TranslatorBundle\Service\Menu\TranslatorMenuAdaptor::class,
            'kunstmaan_translator.service.exporter.csv.class' => \Kunstmaan\TranslatorBundle\Service\Command\Exporter\CSVFileExporter::class,
            'kunstmaan_translator.toolbar.collector.translator.class' => \Kunstmaan\TranslatorBundle\Toolbar\TranslatorDataCollector::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanTranslatorBundle 5.2 and will be removed in KunstmaanTranslatorBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
