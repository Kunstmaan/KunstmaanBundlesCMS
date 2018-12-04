<?php

namespace Kunstmaan\NodeSearchBundle\Tests\EventListener;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener;

class NodeIndexUpdateEventListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateOfChildPageWithStructuredNodeParent()
    {
        $parentNodeTranslation = $this->createMock(StructureNode::class);
        $parentNodeTranslation->method('isOnline')->willReturn(false);

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

        $listener = new NodeIndexUpdateEventListener($this->getContainer($this->getSearchConfiguration(true)));
        $listener->onPostPersist($nodeEvent);
    }

    public function testUpdateOfChildPageWithStructuredNodeParentAndOfflineParent()
    {
        $parentNodeTranslation = $this->createMock(StructureNode::class);
        $parentNodeTranslation->method('isOnline')->willReturn(false);

        $parentNodeTranslation2 = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation2->method('isOnline')->willReturn(false);

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

        $listener = new NodeIndexUpdateEventListener($this->getContainer($this->getSearchConfiguration(false)));
        $listener->onPostPersist($nodeEvent);
    }

    public function testUpdateOfChildPageWithOfflineParent()
    {
        $parentNodeTranslation = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation->method('isOnline')->willReturn(false);

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

        $listener = new NodeIndexUpdateEventListener($this->getContainer($this->getSearchConfiguration(false)));
        $listener->onPostPersist($nodeEvent);
    }

    public function testUpdateOfChildPageWithOnlineParent()
    {
        $parentNodeTranslation = $this->createMock(NodeTranslation::class);
        $parentNodeTranslation->method('isOnline')->willReturn(true);

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

        $listener = new NodeIndexUpdateEventListener($this->getContainer($this->getSearchConfiguration(true)));
        $listener->onPostPersist($nodeEvent);
    }

    private function getContainer($searchConfigMock)
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('kunstmaan_node_search.search_configuration.node'))
            ->willReturn($searchConfigMock)
        ;

        return $container;
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
