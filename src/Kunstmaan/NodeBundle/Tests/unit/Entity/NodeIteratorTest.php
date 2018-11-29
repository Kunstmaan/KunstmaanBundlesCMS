<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeIterator;
use PHPUnit_Framework_TestCase;

/**
 * Class NodeIteratorTest
 */
class NodeIteratorTest extends PHPUnit_Framework_TestCase
{
    public function testNodeIterator()
    {
        $node1 = new Node();
        $node1->setId(1);
        $node2 = new Node();
        $node2->setId(2);
        $node3 = new Node();
        $node3->setId(3);
        $node4 = new Node();
        $node4->setId(4);

        $node1->setChildren(new ArrayCollection([$node4]));

        $collection = new ArrayCollection([
            $node1, $node2, $node3,
        ]);

        $iterator = new NodeIterator($collection);

        $this->assertTrue($iterator->valid());
        $this->assertTrue($iterator->hasChildren());
        $this->assertInstanceOf(NodeIterator::class, $iterator->getChildren());
        $this->assertInstanceOf(Node::class, $iterator->current());
        $iterator->next();

        $this->assertTrue($iterator->valid());
        $this->assertFalse($iterator->hasChildren());
        $this->assertEquals(1, $iterator->key());
        $iterator->next();

        $this->assertTrue($iterator->valid());
        $this->assertFalse($iterator->hasChildren());
        $iterator->next();

        $this->assertFalse($iterator->valid());
        $iterator->rewind();
    }
}
