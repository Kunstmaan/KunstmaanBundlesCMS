<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\RevertNodeAction;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response;
use Kunstmaan\NodeBundle\Tests\Entity\TestEntity;

/**
 * Class RevertNodeActionTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class RevertNodeActionTest extends PHPUnit_Framework_TestCase
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

        $event = new RevertNodeAction($node, $nodeTranslation, $nodeVersion, $page, $nodeVersion, $page);

        $this->assertInstanceOf(Node::class, $event->getNode());
        $this->assertInstanceOf(NodeTranslation::class, $event->getNodeTranslation());
        $this->assertInstanceOf(NodeVersion::class, $event->getNodeVersion());
        $this->assertInstanceOf(TestEntity::class, $event->getPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getOriginNodeVersion());
        $this->assertInstanceOf(TestEntity::class, $event->getOriginPage());

        $event->setNode($node);
        $event->setNodeTranslation($nodeTranslation);
        $event->setNodeVersion($nodeVersion);
        $event->setPage($page);
        $event->setResponse(new Response());
        $event->setOriginNodeVersion($nodeVersion);
        $event->setOriginPage($page);

        $this->assertInstanceOf(Node::class, $event->getNode());
        $this->assertInstanceOf(NodeTranslation::class, $event->getNodeTranslation());
        $this->assertInstanceOf(TestEntity::class, $event->getPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getNodeVersion());
        $this->assertInstanceOf(Response::class, $event->getResponse());
        $this->assertInstanceOf(NodeVersion::class, $event->getOriginNodeVersion());
        $this->assertInstanceOf(TestEntity::class, $event->getOriginPage());
    }
}
