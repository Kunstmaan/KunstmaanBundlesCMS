<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\Tests\EventListener;

use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Kunstmaan\SeoBundle\EventListener\ParameterRobotsTxtListener;
use PHPUnit\Framework\TestCase;

class ParameterRobotsTxtListenerTest extends TestCase
{
    private const CONTENT = 'User-agent: *
Allow: /';

    public function testShouldSetContentWhenEmpty()
    {
        $event = new RobotsEvent();
        $listener = new ParameterRobotsTxtListener('fallback content');
        $listener->__invoke($event);

        $this->assertEquals('fallback content', $event->getContent());
    }

    public function testShouldSetDoNothingWhenFilled()
    {
        $event = new RobotsEvent(self::CONTENT);
        $listener = new ParameterRobotsTxtListener('fallback content');
        $listener->__invoke($event);

        $this->assertEquals(self::CONTENT, $event->getContent());
    }
}
