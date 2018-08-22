<?php

namespace Kunstmaan\VotingBundle\Event;

use Symfony\Component\HttpFoundation\Request;

interface EventInterface
{
    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @return string
     */
    public function getReference();

    /**
     * @return int
     */
    public function getValue();
}
