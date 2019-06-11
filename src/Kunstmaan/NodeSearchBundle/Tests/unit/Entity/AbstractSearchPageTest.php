<?php

namespace Kunstmaan\NodeSearchBundle\Tests\Entity;

use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractSearchPageTest
 */
class AbstractSearchPageTest extends TestCase
{
    public function testGetters()
    {
        $page = new AbstractSearchPage();
        $this->assertEquals('KunstmaanNodeSearchBundle:AbstractSearchPage:service', $page->getControllerAction());
        $this->assertEquals('KunstmaanNodeSearchBundle:AbstractSearchPage:view.html.twig', $page->getDefaultView());
        $this->assertEquals('kunstmaan_node_search.search.node', $page->getSearcher());
        $this->assertFalse($page->isIndexable());
        $this->assertInternalType('array', $page->getPossibleChildTypes());
    }
}
