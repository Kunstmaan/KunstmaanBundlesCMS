<?php

namespace Kunstmaan\AdminBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\MenuCompilerPass;

class KunstmaanAdminBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuCompilerPass());
    }

    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
