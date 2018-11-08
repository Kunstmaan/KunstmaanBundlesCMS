<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SlugEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class SlugEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $event = new SlugEvent(new Response(), new RenderContext());

        $this->assertInstanceOf(Response::class, $event->getResponse());
        $this->assertInstanceOf(RenderContext::class, $event->getRenderContext());

        $event->setRenderContext(new RenderContext());
        $event->setResponse(new Response());

        $this->assertInstanceOf(Response::class, $event->getResponse());
        $this->assertInstanceOf(RenderContext::class, $event->getRenderContext());
    }
}
