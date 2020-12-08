<?php

namespace Kunstmaan\AdminBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

/**
 * This helper will help you to clone Entities
 */
class CloneHelper
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EntityManager $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function deepCloneAndSave($entity)
    {
        $clonedEntity = clone $entity;
        $this->dispatch(new DeepCloneAndSaveEvent($entity, $clonedEntity), Events::DEEP_CLONE_AND_SAVE);

        $this->em->persist($clonedEntity);
        $this->em->flush();

        $this->dispatch(new DeepCloneAndSaveEvent($entity, $clonedEntity), Events::POST_DEEP_CLONE_AND_SAVE);

        return $clonedEntity;
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($this->eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
