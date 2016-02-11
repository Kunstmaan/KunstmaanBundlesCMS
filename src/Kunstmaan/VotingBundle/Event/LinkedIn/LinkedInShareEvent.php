<?php

namespace Kunstmaan\VotingBundle\Event\LinkedIn;

use Kunstmaan\VotingBundle\Event\EventInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event triggered through a callback from the LinkedIn Javascript API when a Share has been executed
 */
class LinkedInShareEvent extends Event implements EventInterface
{

    private $request;

    /**
     * The reference for the Share
     *
     * @var string
     */
    private $reference;

    /**
     * The value of this Share
     *
     * @var int
     */
    private $value;

    public function __construct(Request $request, $reference, $value)
    {
        $this->request = $request;
        $this->reference = $reference;
        $this->value = $value;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

}
