<?php

namespace Kunstmaan\AdminNodeBundle\Listener;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Listens to doctrine postPersist and postUpdate events and updates
 * the urls if the entities are nodetranslations
 */
class NodeTranslationListener
{

    /**
     * Runs the postUpdate doctrine event and updates the nodetranslation urls if needed
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof NodeTranslation) {
            $em     = $args->getEntityManager();

            $entity = $this->updateUrl($entity);
            $this->updateNodeChildren($entity, $em);

            $em->persist($entity);
        }
    }

    /**
     * Runs the postPersist doctrine event and updates the nodetranslation urls if needed
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->postUpdate($args);
    }

    /**
     * Checks if a nodetranslation has children and update their url
     * @param \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation $node
     * @param $em
     */
    private function updateNodeChildren(NodeTranslation $node, $em)
    {
        $children = $node->getNode()->getChildren();
        if (count($children) > 0) {
            foreach ($children as $child) {
                $translation = $child->getNodeTranslation($node->getLang(), true);
                if ($translation) {
                    $translation->setUrl($translation->getFullSlug());
                    $em->persist($translation);
                }
            }
        }
    }

    /**
     * Update the url for a nodetranslation
     * @param \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation $node
     *
     * @return \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation
     */
    private function updateUrl(NodeTranslation $node)
    {
        return $node->setUrl($node->getFullSlug());
    }

}