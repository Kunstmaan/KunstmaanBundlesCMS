<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigureActionMenuEventTest
 */
class CopyPageTranslationNodeEventTest extends TestCase
{
    public function testGetSet()
    {
        /** @var Node $node */
        $node = $this->createMock(Node::class);
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->createMock(NodeTranslation::class);
        /** @var NodeVersion $nodeVersion */
        $nodeVersion = $this->createMock(NodeVersion::class);
        $page = $this->createMock(HasNodeInterface::class);

        $event = new CopyPageTranslationNodeEvent($node, $nodeTranslation, $nodeVersion, $page, $nodeTranslation, $nodeVersion, $page, 'nl');

        $this->assertEquals('nl', $event->getOriginalLanguage());
        $this->assertInstanceOf(NodeTranslation::class, $event->getOriginalNodeTranslation());
        $this->assertInstanceOf(\get_class($page), $event->getOriginalPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getOriginalNodeVersion());

        $event->setOriginalLanguage('nl');
        $event->setOriginalNodeTranslation($nodeTranslation);
        $event->setOriginalNodeVersion($nodeVersion);
        $event->setOriginalPage($page);

        $this->assertEquals('nl', $event->getOriginalLanguage());
        $this->assertInstanceOf(NodeTranslation::class, $event->getOriginalNodeTranslation());
        $this->assertInstanceOf(\get_class($page), $event->getOriginalPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getOriginalNodeVersion());
    }
}
