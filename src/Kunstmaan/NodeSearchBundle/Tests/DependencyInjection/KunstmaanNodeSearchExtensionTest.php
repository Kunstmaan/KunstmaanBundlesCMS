<?php

namespace Kunstmaan\NodeSearchBundle\Tests\DependencyInjection;

use Kunstmaan\NodeSearchBundle\DependencyInjection\KunstmaanNodeSearchExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanNodeSearchExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanNodeSearchExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.indexname', 'nodeindex');
        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.indextype', 'page');
    }
}
