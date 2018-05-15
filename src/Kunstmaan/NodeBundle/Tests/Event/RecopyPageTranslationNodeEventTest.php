<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\RecopyPageTranslationNodeEvent;
use PHPUnit_Framework_TestCase;
use Kunstmaan\NodeBundle\Tests\Entity\TestEntity;

/**
 * Class RecopyPageTranslationNodeEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class RecopyPageTranslationNodeEventTest extends PHPUnit_Framework_TestCase
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

        $event = new RecopyPageTranslationNodeEvent($node, $nodeTranslation, $nodeVersion, $page, $nodeTranslation, $nodeVersion, $page, 'nl');

        $this->assertEquals('nl', $event->getOriginalLanguage());
        $this->assertInstanceOf(NodeTranslation::class, $event->getOriginalNodeTranslation());
        $this->assertInstanceOf(TestEntity::class, $event->getOriginalPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getOriginalNodeVersion());

        $event->setOriginalLanguage('nl');
        $event->setOriginalNodeTranslation($nodeTranslation);
        $event->setOriginalNodeVersion($nodeVersion);
        $event->setOriginalPage($page);

        $this->assertEquals('nl', $event->getOriginalLanguage());
        $this->assertInstanceOf(NodeTranslation::class, $event->getOriginalNodeTranslation());
        $this->assertInstanceOf(TestEntity::class, $event->getOriginalPage());
        $this->assertInstanceOf(NodeVersion::class, $event->getOriginalNodeVersion());
    }

}
