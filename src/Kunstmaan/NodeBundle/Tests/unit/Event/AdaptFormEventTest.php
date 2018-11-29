<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdaptFormEventTest
 */
class AdaptFormEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $request = new Request();
        /** @var TabPane $tabPane */
        $tabPane = $this->createMock(TabPane::class);
        /** @var Node $node */
        $node = $this->createMock(Node::class);
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->createMock(NodeTranslation::class);
        /** @var NodeVersion $nodeVersion */
        $nodeVersion = $this->createMock(NodeVersion::class);

        $event = new AdaptFormEvent($request, $tabPane, 5, $node, $nodeTranslation, $nodeVersion);

        $this->assertInstanceOf(Request::class, $event->getRequest());
        $this->assertInstanceOf(TabPane::class, $event->getTabPane());
        $this->assertEquals(5, $event->getPage());
        $this->assertInstanceOf(Node::class, $event->getNode());
        $this->assertInstanceOf(NodeTranslation::class, $event->getNodeTranslation());
        $this->assertInstanceOf(NodeVersion::class, $event->getNodeVersion());
    }
}
