<?php

namespace Kunstmaan\TaggingBundle\EventListener;

use Doctrine\ORM\EntityManager;
use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;

/**
 * This listener will make sure the tags are copied as well
 */
class CloneListener
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof Taggable) {
            $targetEntity = $event->getClonedEntity();
            $this->em->getRepository('KunstmaanTaggingBundle:Tag')->copyTags($originalEntity, $targetEntity);
        }
    }
}
