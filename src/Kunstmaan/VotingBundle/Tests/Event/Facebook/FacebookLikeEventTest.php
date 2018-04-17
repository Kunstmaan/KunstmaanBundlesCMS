<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FacebookLikeEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class FacebookLikeEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $request = new Request();
        $response = new Response();

        $event = new FacebookLikeEvent($request, $response, 100);

        $this->assertInstanceOf(Request::class, $event->getRequest());
        $this->assertInstanceOf(Response::class, $event->getReference());
        $this->assertEquals(100, $event->getValue());
    }
}
