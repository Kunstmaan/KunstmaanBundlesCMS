<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\HttpFoundation\Response;

class SlugEventTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testDeprecation()
    {
        $this->expectDeprecation('The "Kunstmaan\NodeBundle\Event\SlugEvent" class and the related "kunstmaan_node.preSlugAction" and "kunstmaan_node.postSlugAction" events are deprecated since KunstmaanNodeBundle 5.9 and will be removed in KunstmaanNodeBundle 6.0. Implement the "Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface" interface on the page entity and provide a render service instead.');

        new SlugEvent(null, new RenderContext());
    }

    /**
     * @group legacy
     */
    public function testSupressDeprecation()
    {
        // No deprecations should be triggered.
        $this->expectNotToPerformAssertions();

        new SlugEvent(null, new RenderContext(), false);
    }

    /**
     * @group legacy
     */
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
