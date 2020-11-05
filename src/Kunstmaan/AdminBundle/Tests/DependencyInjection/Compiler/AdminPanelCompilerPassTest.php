<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AdminPanelCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AdminPanelCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AdminPanelCompilerPass());
    }

    public function testContainerKeys()
    {
        $svc = new Definition();
        $svc->addTag('kunstmaan_admin.admin_panel.adaptor');
        $this->setDefinition('kunstmaan_admin.admin_panel', $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_admin.admin_panel',
            'addAdminPanelAdaptor',
            [new Reference('kunstmaan_admin.admin_panel'), 0]
        );
    }
}
