<?php

namespace Kunstmaan\NodeBundle\Tests\Event\UpDown;

use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DownVoteEventTest extends TestCase
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
