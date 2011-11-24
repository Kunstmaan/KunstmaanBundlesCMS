<?php

namespace Kunstmaan\KMediaBundle\Helper\Listener;

use Kunstmaan\KMediaBundle\Entity\Media;
use Kunstmaan\KMediaBundle\Helper\MediaManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class DoctrineMediaListener
{
    /* @var MediaManager */
    private $mediaManager;

    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->prepareMedia($eventArgs->getEntity());
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($this->prepareMedia($entity)) {
            // Hack ? Don't know, that's the behaviour Doctrine 2 seems to want
            // See : http://www.doctrine-project.org/jira/browse/DDC-1020
            $em = $eventArgs->getEntityManager();
            $uow = $em->getUnitOfWork();
            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata(get_class($entity)),
                $eventArgs->getEntity()
            );
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->saveMedia($eventArgs->getEntity(), true);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->saveMedia($eventArgs->getEntity());
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (!$entity instanceof Media) {
            return;
        }
        
        $this->mediaManager->removeMedia($entity);
    }

    private function prepareMedia($entity)
    {
        if (!$entity instanceof Media) {
            return false;
        }

        $this->mediaManager->prepareMedia($entity);
        
        return true;
    }

    private function saveMedia($entity, $new = false)
    {
        if (!$entity instanceof Media) {
            return;
        }

        $this->mediaManager->saveMedia($entity, $new);
    }
}