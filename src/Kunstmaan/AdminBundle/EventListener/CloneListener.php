<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

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
    }

}
