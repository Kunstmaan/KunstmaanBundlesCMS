<?php

namespace Kunstmaan\VotingBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractVoteEvent extends Event implements EventInterface
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
