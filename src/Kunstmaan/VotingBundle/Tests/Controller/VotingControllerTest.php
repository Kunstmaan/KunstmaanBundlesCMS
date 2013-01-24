<?php

namespace Kunstmaan\VotingBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\VotingBundle\EventListener\Facebook\FacebookLikeEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;

class VotingControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $this->assertTrue(true);
    }

}
