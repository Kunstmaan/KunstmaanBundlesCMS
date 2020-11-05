<?php

namespace Kunstmaan\FixturesBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\ParserCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParserCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ParserCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_fixtures.parser.parser';
        $svc = new Definition();
        $svc->addTag('kunstmaan_fixtures.parser.property', ['alias' => 'someAlias']);
        $svc->addTag('kunstmaan_fixtures.parser.spec', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addParser',
            [new Reference($svcId), 'someAlias']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addSpecParser',
            [new Reference($svcId), 'someAlias']
        );
    }
}
