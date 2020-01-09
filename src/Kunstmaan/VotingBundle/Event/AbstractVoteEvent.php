<?php

namespace Kunstmaan\VotingBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

// Clean up when sf 3.4 support is removed
if (is_subclass_of(EventDispatcherInterface::class, ContractsEventDispatcherInterface::class)) {
    abstract class BcEvent extends ContractsEvent
    {
    }
} else {
    abstract class BcEvent extends Event
    {
    }
}

abstract class AbstractVoteEvent extends BcEvent implements EventInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var int
     */
    protected $value;

    /**
     * @param Request $request
     * @param string  $reference
     * @param int     $value
     */
    public function __construct(Request $request, $reference, $value)
    {
        $this->request = $request;
        $this->reference = $reference;
        $this->value = $value;
    }

    /**
     * @return Request
     */
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
