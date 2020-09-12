<?php

namespace Kunstmaan\VotingBundle\Event;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

if (!class_exists(Event::class)) {
    /**
     * Symfony 3.4
     *
     * @internal
     */
    abstract class BcEvent extends LegacyEvent
    {
    }
} else {
    /**
     * Symfony >= 4.3
     *
     * @internal
     */
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
     * @param string $reference
     * @param int    $value
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
