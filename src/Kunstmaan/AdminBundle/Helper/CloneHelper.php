<?php

namespace Kunstmaan\AdminBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This helper will help you to clone Entities
 */
class CloneHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function deepCloneAndSave($entity)
    {
        $clonedEntity = clone $entity;
        $this->eventDispatcher->dispatch(new DeepCloneAndSaveEvent($entity, $clonedEntity), Events::DEEP_CLONE_AND_SAVE);

        $this->em->persist($clonedEntity);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new DeepCloneAndSaveEvent($entity, $clonedEntity), Events::POST_DEEP_CLONE_AND_SAVE);

        return $clonedEntity;
    }
}
