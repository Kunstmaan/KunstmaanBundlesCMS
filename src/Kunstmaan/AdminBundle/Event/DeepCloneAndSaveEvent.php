<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event wil be used to pass metadata when the deep clone event is triggered.
 */
final class DeepCloneAndSaveEvent extends Event
{
    private $entity;

    private $clonedEntity;

    /**
     * @param mixed $entity       The origin entity
     * @param mixed $clonedEntity The cloned entity
     */
    public function __construct($entity, $clonedEntity)
    {
        $this->entity = $entity;
        $this->clonedEntity = $clonedEntity;
    }

    public function setClonedEntity($clonedEntity): DeepCloneAndSaveEvent
    {
        $this->clonedEntity = $clonedEntity;

        return $this;
    }

    public function getClonedEntity()
    {
        return $this->clonedEntity;
    }

    public function setEntity($entity): DeepCloneAndSaveEvent
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
