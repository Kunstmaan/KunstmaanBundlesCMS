<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UseFosUserOptionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $useFosRouting = $container->getParameter('kunstmaan_admin.use_fos_routing');
        $def = $container->getDefinition('twig');
        $def->addMethodCall('addGlobal', array('use_fos_routing', $useFosRouting));

        if($useFosRouting) {
            $container->setParameter('kunstmaan_admin.user_class', $container->getParameter('fos_user.model.user.class'));
        }
    }
}
