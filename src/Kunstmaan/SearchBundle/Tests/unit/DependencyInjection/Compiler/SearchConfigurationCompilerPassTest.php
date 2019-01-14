<?php

namespace Kunstmaan\SearchBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchConfigurationCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SearchConfigurationCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SearchConfigurationCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_search.search_configuration_chain';
        $svc = new Definition();
        $svc->addTag('kunstmaan_search.search_configuration', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addConfiguration',
            [new Reference($svcId), 'someAlias']
        );
    }
}
