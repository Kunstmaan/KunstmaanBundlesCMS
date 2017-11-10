<?php

namespace Kunstmaan\Rest\NodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_api.service.data_transformer')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_api.service.data_transformer');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_api.transformer');

        $transformers = [];
        foreach ($taggedServices as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $transformers[$priority][] = new Reference($id);
        }

        // sort by priority and flatten
        krsort($transformers);
        $transformers = call_user_func_array('array_merge', $transformers);

        foreach ($transformers as $transformer) {
            $definition->addMethodCall(
                'addTransformer',
                array($transformer)
            );
        }
    }
}
