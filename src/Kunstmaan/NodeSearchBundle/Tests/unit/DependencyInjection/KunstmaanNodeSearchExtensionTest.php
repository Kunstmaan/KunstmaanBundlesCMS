<?php

namespace Kunstmaan\NodeSearchBundle\Tests\DependencyInjection;

use Kunstmaan\NodeSearchBundle\DependencyInjection\KunstmaanNodeSearchExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanNodeSearchExtensionTest
 */
class KunstmaanNodeSearchExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanNodeSearchExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.search_configuration.node.class', 'Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration');
        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.indexname', 'nodeindex');
        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.indextype', 'page');
        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.search.node.class', 'Kunstmaan\NodeSearchBundle\Search\NodeSearcher');
        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.search_service.class', 'Kunstmaan\NodeSearchBundle\Services\SearchService');
        $this->assertContainerBuilderHasParameter('kunstmaan_node_search.node_index_update.listener.class', 'Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener');
    }
}
