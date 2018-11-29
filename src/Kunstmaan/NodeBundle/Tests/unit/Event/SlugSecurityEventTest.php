<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Codeception\Stub;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlugSecurityEventTest
 */
class SlugSecurityEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        /** @var Node $node */
        $node = $this->createMock(Node::class);
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->createMock(NodeTranslation::class);
        /** @var HasNodeInterface $page */
        $page = Stub::makeEmpty(HasNodeInterface::class);

        $event = new SlugSecurityEvent();

        $event->setNode($node);
        $event->setNodeTranslation($nodeTranslation);
        $event->setEntity($page);
        $event->setRequest(new Request());

        $this->assertInstanceOf(Node::class, $event->getNode());
        $this->assertInstanceOf(NodeTranslation::class, $event->getNodeTranslation());
        $this->assertInstanceOf(get_class($page), $event->getEntity());
        $this->assertInstanceOf(Request::class, $event->getRequest());
    }
}
