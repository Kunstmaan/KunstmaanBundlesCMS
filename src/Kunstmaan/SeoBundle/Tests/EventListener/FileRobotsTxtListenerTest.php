<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\Tests\EventListener;

use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Kunstmaan\SeoBundle\EventListener\FileRobotsTxtListener;
use PHPUnit\Framework\TestCase;

final class FileRobotsTxtListenerTest extends TestCase
{
    private const CONTENT = 'User-agent: *
Allow: /';

    public function testShouldDoNothingWhenFilled(): void
    {
        $event = new RobotsEvent();
        $event->setContent(self::CONTENT);
        $listener = new FileRobotsTxtListener(__FILE__);
        $listener->__invoke($event);

        $this->assertEquals(self::CONTENT, $event->getContent());
    }

    public function testShouldSetContentFromFileWhenEmpty(): void
    {
        $event = new RobotsEvent();
        $listener = new FileRobotsTxtListener(__FILE__);
        $listener->__invoke($event);

        $this->assertEquals(file_get_contents(__FILE__), $event->getContent());
    }

    public function testShouldDoNothingWhenFileDoesNotExists(): void
    {
        $event = new RobotsEvent();
        $listener = new FileRobotsTxtListener('/some/none/existing/file');
        $listener->__invoke($event);

        $this->assertEquals('', $event->getContent());
    }
}
