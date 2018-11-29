<?php

namespace Kunstmaan\NodeSearchBundle\Tests\Entity;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeSearchBundle\Entity\NodeSearch;
use PHPUnit_Framework_TestCase;

/**
 * Class NodeSearchTest
 */
class NodeSearchTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $search = new NodeSearch();
        $search->setNode(new Node());
        $search->setBoost(3.141);

        $this->assertInstanceOf(Node::class, $search->getNode());
        $this->assertEquals(3.141, $search->getBoost());
    }
}
