<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\Tests\Event;

use Kunstmaan\SeoBundle\Event\RobotsEvent;
use PHPUnit\Framework\TestCase;

class RobotsEventTest extends TestCase
{
    public function testShouldAllowSettingContext(): void
    {
        $initialContent = 'Current content';
        $object = new RobotsEvent($initialContent);

        $result = $object->getContent();
        $this->assertEquals($initialContent, $result);

        $newContent = "$result\nAdded";
        $object->setContent($newContent);

        $this->assertEquals($newContent, $object->getContent());
    }

    public function testShouldDefaultToEmptyContent(): void
    {
        $object = new RobotsEvent();

        $result = $object->getContent();
        $this->assertEquals('', $result);
    }
}
