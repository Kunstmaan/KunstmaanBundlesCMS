<?php

namespace Kunstmaan\AdminBundle\Tests\Event;

use DateTime;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use PHPUnit\Framework\TestCase;
use stdClass;

class DeepCloneAndSaveEventTest extends TestCase
{
    public function testGetSet()
    {
        $date = new DateTime();
        $clone = clone $date;

        $event = new DeepCloneAndSaveEvent($date, $clone);

        $this->assertInstanceOf(DateTime::class, $event->getEntity());
        $this->assertInstanceOf(DateTime::class, $event->getClonedEntity());

        $std = new stdClass();
        $clone = clone $std;
        $event->setEntity($std);
        $event->setClonedEntity($clone);

        $this->assertInstanceOf(stdClass::class, $event->getEntity());
        $this->assertInstanceOf(stdClass::class, $event->getClonedEntity());
    }
}
