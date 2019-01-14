<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\MenuCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MenuCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MenuCompilerPass());
    }

    public function testContainerKeys()
    {
        $svc = new Definition();
        $svc->addTag('kunstmaan_admin.menu.adaptor');
        $this->setDefinition('kunstmaan_admin.menubuilder', $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_admin.menubuilder',
            'addAdaptMenu',
            [new Reference('kunstmaan_admin.menubuilder'), 0]
        );
    }
}
