<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexNodeEventTest
 */
class IndexNodeEventTest extends TestCase
{
    public function testGetSet()
    {
        $page = $this->createMock(HasNodeInterface::class);

        $event = new IndexNodeEvent($page, ['test' => 'value']);

        $this->assertCount(1, $event->doc);
        $this->assertInstanceOf(\get_class($page), $event->getPage());
    }
}
