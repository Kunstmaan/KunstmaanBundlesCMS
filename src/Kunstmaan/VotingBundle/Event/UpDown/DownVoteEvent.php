<?php

namespace Kunstmaan\VotingBundle\Event\UpDown;

use Kunstmaan\VotingBundle\Event\EventInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event when a Down vote has been triggered
 */
class DownVoteEvent extends Event implements EventInterface
{

    private $request;

    private $reference;

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
