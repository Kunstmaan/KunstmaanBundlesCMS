<?php

namespace Tests\Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Kunstmaan\NodeBundle\Entity\TestEntity;

/**
 * Class IndexNodeEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class IndexNodeEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $page = new TestEntity();

        $event = new IndexNodeEvent($page, ['test' => 'value']);

        $this->assertCount(1,$event->doc);
        $this->assertInstanceOf(TestEntity::class, $event->getPage());
    }
}
