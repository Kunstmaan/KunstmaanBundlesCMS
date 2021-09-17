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

    /**
     * @group legacy
     */
    public function testInstantiationDeprecation()
    {
        $this->expectDeprecation('Instantiating the "Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage" class is deprecated in KunstmaanNodeSearchBundle 5.9 and will be made abstract in KunstmaanNodeSearchBundle 6.0. Extend your implementation from this class instead.');

        new AbstractSearchPage();
    }
}
