<?php

namespace Kunstmaan\FixturesBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\BuilderCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BuilderCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BuilderCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_fixtures.builder.builder';
        $svc = new Definition();
        $svc->addTag('kunstmaan_fixtures.builder', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addBuilder',
            [new Reference($svcId), 'someAlias']
        );
    }
}
