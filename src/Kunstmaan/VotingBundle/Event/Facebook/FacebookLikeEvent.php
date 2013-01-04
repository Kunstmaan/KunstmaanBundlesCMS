<?php

namespace Kunstmaan\VotingBundle\Event\Facebook;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event triggered through a callback from the Facebook API when a Like has been executed
 */
class FacebookLikeEvent extends Event
{

    private $request;

    /**
     * The response returned by the Facebook API callback when a Like has been registered
     *
     * @var string
     */
    private $response;


    public function __construct(Request $request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

}