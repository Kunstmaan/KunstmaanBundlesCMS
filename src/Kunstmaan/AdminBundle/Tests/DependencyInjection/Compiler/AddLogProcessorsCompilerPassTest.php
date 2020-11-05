<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AddLogProcessorsCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddLogProcessorsCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddLogProcessorsCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_admin.logger';
        $svc = new Definition();
        $svc->addTag('kunstmaan_admin.logger.processor');
        $svc->addTag('kunstmaan_admin.logger.processor', ['method' => 'someMethod']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'pushProcessor',
            [new Reference($svcId)]
        );
    }
}
