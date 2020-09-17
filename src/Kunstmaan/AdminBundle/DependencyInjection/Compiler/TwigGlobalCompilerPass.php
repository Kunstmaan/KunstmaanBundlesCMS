<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigGlobalCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $useFosRouting = $container->getParameter('kunstmaan_admin.use_fos_routing');
        $def = $container->getDefinition('twig');
        $def->addMethodCall('addGlobal', array('use_fos_routing', $useFosRouting));
    }
}
