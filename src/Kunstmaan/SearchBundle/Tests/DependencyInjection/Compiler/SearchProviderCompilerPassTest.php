<?php

namespace Kunstmaan\SearchBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchProviderCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SearchProviderCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SearchProviderCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_search.search_provider_chain';
        $svc = new Definition();
        $svc->addTag('kunstmaan_search.search_provider', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addProvider',
            [new Reference($svcId), 'someAlias']
        );
    }
}
