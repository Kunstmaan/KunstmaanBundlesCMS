<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TransformerCompilerPass
 */
class TransformerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_rest_core.service.data_transformer')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_rest_core.service.data_transformer');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_rest.transformer');

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
