<?php

namespace Kunstmaan\AdminListBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AdminListEvent extends Event
{
    /**
     * @var object
     */
    protected $entity;

    /**
     * AdminListEvent constructor.
     * @param object $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
