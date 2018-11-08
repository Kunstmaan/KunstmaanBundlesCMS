<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DownVoteEventTest
 * @package Tests\Kunstmaan\NodeBundle\Event
 */
class DownVoteEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $request = new Request();
        $response = new Response();

        $event = new DownVoteEvent($request, $response, 100);

        $this->assertInstanceOf(Request::class, $event->getRequest());
        $this->assertInstanceOf(Response::class, $event->getReference());
        $this->assertEquals(100, $event->getValue());
    }
}
