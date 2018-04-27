<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpVoteEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class UpVoteEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $request = new Request();
        $response = new Response();

        $event = new UpVoteEvent($request, $response, 100);

        $this->assertInstanceOf(Request::class, $event->getRequest());
        $this->assertInstanceOf(Response::class, $event->getReference());
        $this->assertEquals(100, $event->getValue());
    }
}
