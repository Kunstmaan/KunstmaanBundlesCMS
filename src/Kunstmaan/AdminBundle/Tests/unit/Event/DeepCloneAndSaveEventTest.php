<?php

namespace Kunstmaan\AdminBundle\Tests\Event;

use DateTime;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Class DeepCloneAndSaveEventTest
 */
class DeepCloneAndSaveEventTest extends PHPUnit_Framework_TestCase
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
