<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddLogProcessorsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_admin.logger')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_admin.logger');

        foreach ($container->findTaggedServiceIds('kunstmaan_admin.logger.processor') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!empty($tag['method'])) {
                    $processor = array(new Reference($id), $tag['method']);
                } else {
                    // If no method is defined, fallback to use __invoke
                    $processor = new Reference($id);
                }
                $definition->addMethodCall('pushProcessor', array($processor));
            }
        }
    }

}
