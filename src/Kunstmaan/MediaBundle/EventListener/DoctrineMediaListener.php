<?php

namespace Kunstmaan\MediaBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * DoctrineMediaListener
 */
class DoctrineMediaListener
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * @var array
     */
    private $fileUrlMap = [];

    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->prepareMedia($eventArgs->getEntity());
    }

    /**
     * @param object $entity
     */
    private function prepareMedia($entity): bool
    {
        if (!$entity instanceof Media) {
            return false;
        }

        $this->mediaManager->prepareMedia($entity);

        return true;
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
                $em->getClassMetadata(ClassLookup::getClass($entity)),
                $eventArgs->getEntity()
            );

            // local media is soft-deleted or soft-delete is reverted
            $changeSet = $eventArgs->getEntityChangeSet();
            if (isset($changeSet['deleted']) && $entity->getLocation() === 'local') {
                $deleted = (!$changeSet['deleted'][0] && $changeSet['deleted'][1]);
                $reverted = ($changeSet['deleted'][0] && !$changeSet['deleted'][1]);
                if ($deleted || $reverted) {
                    $oldFileUrl = $entity->getUrl();
                    $newFileName = ($reverted ? $entity->getOriginalFilename() : uniqid());
                    $newFileUrl = \dirname($oldFileUrl) . '/' . $newFileName . '.' . pathinfo($oldFileUrl, PATHINFO_EXTENSION);
                    $entity->setUrl($newFileUrl);
                    $this->fileUrlMap[$newFileUrl] = $oldFileUrl;
                }
            }
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->saveMedia($eventArgs->getEntity(), true);
    }

    /**
     * @param object $entity The entity
     * @param bool   $new    Is new
     */
    private function saveMedia($entity, $new = false)
    {
        if (!$entity instanceof Media) {
            return;
        }

        $this->mediaManager->saveMedia($entity, $new);
        $url = $entity->getUrl();
        $handler = $this->mediaManager->getHandler($entity);
        if (isset($this->fileUrlMap[$url]) && $handler instanceof FileHandler) {
            $regex = '~^' . preg_quote($handler->mediaPath, '~') . '~';
            $originalFileName = preg_replace($regex, '/', $this->fileUrlMap[$url]);
            // Check if file exists on filesystem.
            if ($handler->fileSystem->has($originalFileName)) {
                $handler->fileSystem->rename(
                    $originalFileName,
                    preg_replace($regex, '/', $url)
                );
            }
            unset($this->fileUrlMap[$url]);
        }
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->saveMedia($eventArgs->getEntity());
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
    }
}
