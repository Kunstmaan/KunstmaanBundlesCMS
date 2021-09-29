<?php

namespace Kunstmaan\NodeSearchBundle\Tests\Entity;

use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class AbstractSearchPageTest extends TestCase
{
    use ExpectDeprecationTrait;

    public function testGetters()
    {
        $page = new class() extends AbstractSearchPage {};
        $this->assertEquals('KunstmaanNodeSearchBundle:AbstractSearchPage:service', $page->getControllerAction());
        $this->assertEquals('@KunstmaanNodeSearch/AbstractSearchPage/view.html.twig', $page->getDefaultView());
        $this->assertEquals('kunstmaan_node_search.search.node', $page->getSearcher());
        $this->assertFalse($page->isIndexable());
        $this->assertIsArray($page->getPossibleChildTypes());
    }
}
