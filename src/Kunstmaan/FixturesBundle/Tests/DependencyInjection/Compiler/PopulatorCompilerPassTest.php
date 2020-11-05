<?php

namespace Kunstmaan\FixturesBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\PopulatorCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PopulatorCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PopulatorCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_fixtures.populator.populator';
        $svc = new Definition();
        $svc->addTag('kunstmaan_fixtures.populator', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);
        $this->setDefinition('kunstmaan_fixtures.builder.builder', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addPopulator',
            [new Reference($svcId), 'someAlias']
        );
    }
}
