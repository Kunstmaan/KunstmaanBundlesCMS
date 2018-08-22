<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response;
use Kunstmaan\NodeBundle\Tests\Entity\TestEntity;

/**
 * Class NodeEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class NodeEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        /** @var Node $node */
        $node = $this->createMock(Node::class);
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->createMock(NodeTranslation::class);
        /** @var NodeVersion $nodeVersion */
        $nodeVersion = $this->createMock(NodeVersion::class);
        $page = new TestEntity();

        $event = new NodeEvent($node, $nodeTranslation, $nodeVersion, $page);

        $this->assertInstanceOf(Node::class, $event->getNode());
        $this->assertInstanceOf(NodeTranslation::class, $event->getNodeTranslation());
        $this->assertInstanceOf(TestEntity::class, $event->getPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getNodeVersion());

        $event->setNode($node);
        $event->setNodeTranslation($nodeTranslation);
        $event->setNodeVersion($nodeVersion);
        $event->setPage($page);
        $event->setResponse(new Response());

        $this->assertInstanceOf(Node::class, $event->getNode());
        $this->assertInstanceOf(NodeTranslation::class, $event->getNodeTranslation());
        $this->assertInstanceOf(TestEntity::class, $event->getPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getNodeVersion());
        $this->assertInstanceOf(Response::class, $event->getResponse());
    }
}
