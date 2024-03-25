<?php

namespace Kunstmaan\TaggingBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\TaggingBundle\Entity\Tag;

/**
 * This listener will make sure the tags are copied as well
 */
class CloneListener
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof Taggable) {
            $targetEntity = $event->getClonedEntity();
            $this->em->getRepository(Tag::class)->copyTags($originalEntity, $targetEntity);
        }
    }
}
