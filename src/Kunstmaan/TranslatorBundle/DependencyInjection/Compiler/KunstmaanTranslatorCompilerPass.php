<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class KunstmaanTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderRefs = array();

        // look for all tagged translation file loaders, inject them into the importer
        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attributes) {
            $loaderAliases[$id][] = $attributes[0]['alias'];
            $loaderRefs[$attributes[0]['alias']] = new Reference($id);

            if(isset($attributes[0]['legacy-alias'])) {
                $loaderAliases[$id][] = $attributes[0]['legacy-alias'];
                $loaderRefs[$attributes[0]['legacy-alias']] = new Reference($id);
            }
        }


        if ($container->hasDefinition('kunstmaan_translator.service.importer.importer')) {
            $container->getDefinition('kunstmaan_translator.service.importer.importer')->addMethodCall('setLoaders', array($loaderRefs));
        }

        if ($container->hasDefinition('kunstmaan_translator.service.translator.translator')) {
            $container->getDefinition('kunstmaan_translator.service.translator.translator')->replaceArgument(2, $loaderAliases);
        }
    }
}