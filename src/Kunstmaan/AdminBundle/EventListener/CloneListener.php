<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Event\DeepCloneEvent;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

class CloneListener
{

    public function onDeepClone(DeepCloneEvent $event)
    {
        $clonedEntity = $event->getClonedEntity();

        if ($clonedEntity instanceof AbstractEntity) {
            $clonedEntity->setId(null);
        }
    }

}
