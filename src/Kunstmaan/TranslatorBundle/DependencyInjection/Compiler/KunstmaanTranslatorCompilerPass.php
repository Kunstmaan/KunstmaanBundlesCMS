<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class KunstmaanTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderRefs = array();
        $loaderAliases = array();
        $exporterRefs = array();

        foreach ($container->findTaggedServiceIds('translation.loader', true) as $id => $attributes) {
            $loaderRefs[$id] = new Reference($id);
            $loaders[$id][] = $attributes[0]['alias'];
            if (isset($attributes[0]['legacy-alias'])) {
                $loaders[$id][] = $attributes[0]['legacy-alias'];
            }
        }

        if ($container->hasDefinition('kunstmaan_translator.service.importer.importer')) {
            $definition = $container->getDefinition('kunstmaan_translator.service.importer.importer');
            foreach ($loaders as $id => $formats) {
                foreach ($formats as $format) {
                    $definition->addMethodCall('addLoader', array($format, $loaderRefs[$id]));
                }
            }
        }

        if ($container->hasDefinition('kunstmaan_translator.service.translator.translator')) {
            //Create custom ServiceLocator to inject in the translator
            $serviceIds = array_merge($loaderRefs, ['request_stack' => new Reference('request_stack')]);
            $serviceLocator = ServiceLocatorTagPass::register($container, $serviceIds);

            $container->getDefinition('kunstmaan_translator.service.translator.translator')
                ->replaceArgument(0, $serviceLocator)
                ->replaceArgument(3, $loaders);
        }

        // add all exporter into the translation exporter
        foreach ($container->findTaggedServiceIds('translation.exporter') as $id => $attributes) {
            $exporterRefs[$attributes[0]['alias']] = new Reference($id);
        }

        if ($container->hasDefinition('kunstmaan_translator.service.exporter.exporter')) {
            $container->getDefinition('kunstmaan_translator.service.exporter.exporter')->addMethodCall('setExporters', array($exporterRefs));
        }
    }
}
