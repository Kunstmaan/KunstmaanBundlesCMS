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
        $this->assertSame('@KunstmaanNodeSearch/AbstractSearchPage/view.html.twig', $page->getDefaultView());
        $this->assertSame('kunstmaan_node_search.search.node', $page->getSearcher());
        $this->assertFalse($page->isIndexable());
        $this->assertIsArray($page->getPossibleChildTypes());
    }
}
