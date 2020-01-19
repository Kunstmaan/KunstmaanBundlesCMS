<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * This event wil be used to pass metadata when the deep clone event is triggered.
 */
class DeepCloneAndSaveEvent extends Event
{
    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var mixed
     */
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

    /**
     * @param mixed $clonedEntity
     *
     * @return DeepCloneAndSaveEvent
     */
    public function setClonedEntity($clonedEntity)
    {
        $this->clonedEntity = $clonedEntity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClonedEntity()
    {
        return $this->clonedEntity;
    }

    /**
     * @param mixed $entity
     *
     * @return DeepCloneAndSaveEvent
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
