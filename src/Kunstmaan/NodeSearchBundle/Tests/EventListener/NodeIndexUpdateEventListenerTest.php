<?php

namespace Kunstmaan\NodeSearchBundle\Tests\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener;
use PHPUnit\Framework\TestCase;

class NodeIndexUpdateEventListenerTest extends TestCase
{
    public function testUpdateOfChildPageWithStructuredNodeParent()
    {
        $parentPage = $this->createMock(StructureNode::class);

        $parentNodeTranslation = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation->method('getRef')->willReturn($parentPage);
        $parentNodeTranslation->method('isOnline')->willReturn(true);

        $parentNode = $this->createMock(Node::class);
        $parentNode->method('getNodeTranslation')->willReturn($parentNodeTranslation);

        $node = $this->createMock(Node::class);
        $node->method('getParents')->willReturn([
            $parentNode,
        ]);

        $nodeTranslation = $this->createMock(NodeTranslation::class);
        $nodeTranslation->method('getId')->willReturn(1);
        $nodeTranslation->method('getLang')->willReturn('nl');
        $nodeTranslation->method('getNode')->willReturn($node);

        $nodeEvent = $this->getMockBuilder(NodeEvent::class)->disableOriginalConstructor()->getMock();

        $nodeEvent->method('getNodeTranslation')->willReturn($nodeTranslation);

        $em = $this->createMock(EntityManager::class);
        $listener = new NodeIndexUpdateEventListener($this->getSearchConfiguration(true), $em);
        $listener->onPostPersist($nodeEvent);
    }

    public function testUpdateOfChildPageWithStructuredNodeParentAndOfflineParent()
    {
        $parentPage = $this->createMock(StructureNode::class);

        $parentNodeTranslation = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation->method('getRef')->willReturn($parentPage);
        $parentNodeTranslation->method('isOnline')->willReturn(true);

        $parentPageNotStructured = $this->createMock(AbstractPage::class);
        $parentNodeTranslation2 = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation2->method('isOnline')->willReturn(false);
        $parentNodeTranslation->method('getRef')->willReturn($parentPageNotStructured);

        $parentNode1 = $this->createMock(Node::class);
        $parentNode1->method('getNodeTranslation')->willReturn($parentNodeTranslation);
        $parentNode2 = $this->createMock(Node::class);
        $parentNode2->method('getNodeTranslation')->willReturn($parentNodeTranslation2);

        $node = $this->createMock(Node::class);
        $node->method('getParents')->willReturn([
            $parentNode1,
            $parentNode2,
        ]);

        $nodeTranslation = $this->createMock(NodeTranslation::class);
        $nodeTranslation->method('getId')->willReturn(1);
        $nodeTranslation->method('getLang')->willReturn('nl');
        $nodeTranslation->method('getNode')->willReturn($node);

        $nodeEvent = $this->getMockBuilder(NodeEvent::class)->disableOriginalConstructor()->getMock();

        $nodeEvent->method('getNodeTranslation')->willReturn($nodeTranslation);

        $em = $this->createMock(EntityManager::class);
        $listener = new NodeIndexUpdateEventListener($this->getSearchConfiguration(false), $em);
        $listener->onPostPersist($nodeEvent);
    }

    public function testUpdateOfChildPageWithOfflineParent()
    {
        $parentPage = $this->createMock(AbstractPage::class);
        $parentNodeTranslation = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation->method('isOnline')->willReturn(false);
        $parentNodeTranslation->method('getRef')->willReturn($parentPage);

        $parentNode = $this->createMock(Node::class);
        $parentNode->method('getNodeTranslation')->willReturn($parentNodeTranslation);

        $node = $this->createMock(Node::class);
        $node->method('getParents')->willReturn([$parentNode]);

        $nodeTranslation = $this->createMock(NodeTranslation::class);
        $nodeTranslation->method('getId')->willReturn(1);
        $nodeTranslation->method('getLang')->willReturn('nl');
        $nodeTranslation->method('getNode')->willReturn($node);

        $nodeEvent = $this->getMockBuilder(NodeEvent::class)->disableOriginalConstructor()->getMock();

        $nodeEvent->method('getNodeTranslation')->willReturn($nodeTranslation);

        $em = $this->createMock(EntityManager::class);
        $listener = new NodeIndexUpdateEventListener($this->getSearchConfiguration(false), $em);
        $listener->onPostPersist($nodeEvent);
    }

    public function testUpdateOfChildPageWithOnlineParent()
    {
        $parentPage = $this->createMock(AbstractPage::class);
        $parentNodeTranslation = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation->method('isOnline')->willReturn(true);
        $parentNodeTranslation->method('getRef')->willReturn($parentPage);

        $parentNode = $this->createMock(Node::class);
        $parentNode->method('getNodeTranslation')->willReturn($parentNodeTranslation);

        $node = $this->createMock(Node::class);
        $node->method('getParents')->willReturn([$parentNode]);

        $nodeTranslation = $this->createMock(NodeTranslation::class);
        $nodeTranslation->method('getId')->willReturn(1);
        $nodeTranslation->method('getLang')->willReturn('nl');
        $nodeTranslation->method('getNode')->willReturn($node);

        $nodeEvent = $this->getMockBuilder(NodeEvent::class)->disableOriginalConstructor()->getMock();

        $nodeEvent->method('getNodeTranslation')->willReturn($nodeTranslation);

        $em = $this->createMock(EntityManager::class);
        $listener = new NodeIndexUpdateEventListener($this->getSearchConfiguration(true), $em);
        $listener->onPostPersist($nodeEvent);
    }

    private function getSearchConfiguration($expectCall)
    {
        $searchConfig = $this->createMock(NodePagesConfiguration::class);
        $searchConfig
            ->expects($expectCall ? $this->once() : $this->never())
            ->method('indexNodeTranslation')
            ->willReturn(null)
        ;

        return $searchConfig;
    }
}
