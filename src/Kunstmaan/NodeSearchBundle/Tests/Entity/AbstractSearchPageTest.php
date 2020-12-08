<?php

namespace Kunstmaan\NodeSearchBundle\Tests\Entity;

use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use PHPUnit\Framework\TestCase;

class AbstractSearchPageTest extends TestCase
{
    public function testGetters()
    {
        $page = new AbstractSearchPage();
        $this->assertEquals('KunstmaanNodeSearchBundle:AbstractSearchPage:service', $page->getControllerAction());
        $this->assertEquals('@KunstmaanNodeSearch/AbstractSearchPage/view.html.twig', $page->getDefaultView());
        $this->assertEquals('kunstmaan_node_search.search.node', $page->getSearcher());
        $this->assertFalse($page->isIndexable());
        $this->assertIsArray($page->getPossibleChildTypes());
    }
}
