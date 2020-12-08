<?php

namespace Kunstmaan\NodeBundle\Tests\Event\LinkedIn;

use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkedInShareEventTest extends TestCase
{
    public function testGetSet()
    {
        $request = new Request();
        $response = new Response();

        $event = new LinkedInShareEvent($request, $response, 100);

        $this->assertInstanceOf(Request::class, $event->getRequest());
        $this->assertInstanceOf(Response::class, $event->getReference());
        $this->assertEquals(100, $event->getValue());
    }
}
