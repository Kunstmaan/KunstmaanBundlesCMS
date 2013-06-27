<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class KunstmaanTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderRefs = array();

        // look for all translation file loaders
        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attributes) {
            $loaderRefs[$attributes[0]['alias']] = new Reference($id);
        }

        if ($container->hasDefinition('kunstmaan_translator.service.importer.importer')) {
            $container->findDefinition('kunstmaan_translator.service.importer.importer')->addMethodCall('setLoaders', array($loaderRefs));
        }
    }
}