<?php

namespace Kunstmaan\AdminBundle\Helper;

use Doctrine\ORM\EntityManager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\Event\DeepCloneEvent;

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

    /**
     * @param EntityManager $em
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param mixed $entity
     */
    public function deepClone($entity)
    {
        $clonedEntity = clone $entity;
        $this->eventDispatcher->dispatch(Events::DEEP_CLONE, new DeepCloneEvent($entity, $clonedEntity, $this->em));

        $this->em->persist($clonedEntity);
        $this->em->flush();

        $this->eventDispatcher->dispatch(Events::POST_DEEP_CLONE, new DeepCloneEvent($entity, $clonedEntity, $this->em));
    }

}
