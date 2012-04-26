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

            if ($entity != false) {
                $em->persist($entity);
                $em->flush();

                $this->updateNodeChildren($entity, $em);
            }
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
                    $translation = $this->updateUrl($translation);

                    if ($translation != false) {
                        $em->persist($translation);
                        $em->flush();

                        $this->updateNodeChildren($translation, $em);
                    }
                }
            }
        }
    }

    /**
     * Update the url for a nodetranslation
     * @param \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation $node
     *
     * @return \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation|bool
     */
    private function updateUrl(NodeTranslation $node)
    {
        $fullSlug   = $node->getFullSlug();
        $fullUrl    = $node->getUrl();

        if ($fullSlug !== $fullUrl) {
            $node->setUrl($fullSlug);

            return $node;
        }

        return false;
    }

}