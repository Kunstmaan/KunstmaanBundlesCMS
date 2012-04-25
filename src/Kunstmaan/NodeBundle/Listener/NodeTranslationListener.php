<?php

namespace Kunstmaan\AdminNodeBundle\Listener;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Doctrine\ORM\Event\LifecycleEventArgs;

class NodeTranslationListener
{

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof NodeTranslation) {
            $entity->setUrl($entity->getFullSlug());

            $children = $entity->getNode()->getChildren();
            if (count($children) > 0) {
                foreach($children as $child) {
                    $translation = $child->getNodeTranslation($entity->getLang(), true);
                    if ($translation) {
                        $translation->setUrl($translation->getFullSlug());
                        $em->persist($translation);
                    }
                }
            }
        }
    }
}