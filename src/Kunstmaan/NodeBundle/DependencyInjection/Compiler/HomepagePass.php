<?php
/**
 * Created by PhpStorm.
 * User: Gabe
 * Date: 2017.01.23.
 * Time: 11:24
 */

namespace Kunstmaan\NodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HomepagePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('kunstmaan_node.pages_configuration')) {
            return;
        }

        $definition = $container->findDefinition('kunstmaan_node.pages_configuration');
        $definition->addMethodCall('setDoctrine', [new Reference('doctrine')]);
        $definition->addMethodCall('initConfiguration');
    }
}
