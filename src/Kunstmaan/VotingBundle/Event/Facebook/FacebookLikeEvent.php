<?php

namespace Kunstmaan\VotingBundle\Event\Facebook;

use Kunstmaan\VotingBundle\Event\EventInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event triggered through a callback from the Facebook API when a Like has been executed
 */
class FacebookLikeEvent extends Event implements EventInterface
{

    private $request;

    /**
     * The response returned by the Facebook API callback when a Like has been registered
     *
     * @var string
     */
    private $response;

    /**
     * The value of this like
     *
     * @var int
     */
    private $value;

    public function __construct(Request $request, $response, $value)
    {
        $this->request = $request;
        $this->response = $response;
        $this->value = $value;
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

    /**
    * @return string
    */
    public function getReference()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

}
