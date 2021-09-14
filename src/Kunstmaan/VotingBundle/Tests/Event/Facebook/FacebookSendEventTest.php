<?php

namespace Kunstmaan\VotingBundle\Tests\Event\Facebook;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FacebookSendEventTest extends TestCase
{
    public function testGetSet()
    {
        $request = new Request();
        $response = new Response();

        $event = new FacebookSendEvent($request, $response, 100);

        $this->assertInstanceOf(Request::class, $event->getRequest());
        $this->assertInstanceOf(Response::class, $event->getReference());
        $this->assertEquals(100, $event->getValue());
    }
}
