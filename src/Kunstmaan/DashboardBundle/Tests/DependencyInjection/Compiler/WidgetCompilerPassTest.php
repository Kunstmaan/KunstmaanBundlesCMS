<?php

namespace Kunstmaan\DashboardBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\DashboardBundle\DependencyInjection\Compiler\WidgetCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class WidgetCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new WidgetCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_dashboard.manager.widgets';
        $svc = new Definition();
        $svc->addTag('kunstmaan_dashboard.widget');
        $svc->addTag('kunstmaan_dashboard.widget', ['method' => 'someMethod']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addWidget',
            [new Reference($svcId)]
        );
    }
}
