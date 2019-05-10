<?php

namespace Kunstmaan\FixturesBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ProviderCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProviderCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_fixtures.builder.builder';
        $svc = new Definition();
        $svc->addTag('kunstmaan_fixtures.provider', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addProvider',
            [new Reference($svcId), 'someAlias']
        );
    }
}
