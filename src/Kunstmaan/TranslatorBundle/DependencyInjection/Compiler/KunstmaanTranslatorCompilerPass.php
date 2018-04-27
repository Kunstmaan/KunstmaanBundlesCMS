<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection\Compiler;

use Kunstmaan\TranslatorBundle\Service\Command\Exporter\Exporter;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\Importer;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class KunstmaanTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderRefs = array();
        $loaderAliases = array();
        $exporterRefs = array();

        // look for all tagged translation file loaders, inject them into the importer
        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attributes) {
            $loaderAliases[$id][] = $attributes[0]['alias'];
            $loaderRefs[$attributes[0]['alias']] = new Reference($id);

            if (isset($attributes[0]['legacy-alias'])) {
                $loaderAliases[$id][] = $attributes[0]['legacy-alias'];
                $loaderRefs[$attributes[0]['legacy-alias']] = new Reference($id);
            }
        }

        if ($container->hasDefinition(Importer::class)) {
            $container->getDefinition(Importer::class)->addMethodCall('setLoaders', array($loaderRefs));
        }

        if ($container->hasDefinition(Translator::class)) {
            $container->getDefinition(Translator::class)->replaceArgument(2, $loaderAliases);
        }

        // add all exporter into the translation exporter
        foreach ($container->findTaggedServiceIds('translation.exporter') as $id => $attributes) {
            $exporterRefs[$attributes[0]['alias']] = new Reference($id);
        }

        if ($container->hasDefinition(Exporter::class)) {
            $container->getDefinition(Exporter::class)->addMethodCall('setExporters', array($exporterRefs));
        }
    }
}
