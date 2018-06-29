<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use PHPUnit_Framework_TestCase;
use Kunstmaan\NodeBundle\Tests\Entity\TestEntity;

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
