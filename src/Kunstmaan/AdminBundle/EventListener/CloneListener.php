<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;

/**
 * This listener will make sure the id isn't copied for AbstractEntities
 */
class CloneListener
{
    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function onDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $clonedEntity = $event->getClonedEntity();

        if ($clonedEntity instanceof AbstractEntity) {
            $clonedEntity->setId(null);
        }
        if ($clonedEntity instanceof DeepCloneInterface) {
            $clonedEntity->deepClone();
        }
    }
}
