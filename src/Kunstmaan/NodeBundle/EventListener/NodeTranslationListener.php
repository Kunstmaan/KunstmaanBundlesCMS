<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Event\OnFlushEventArgs;

use Doctrine\ORM\Event\PostFlushEventArgs;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;

/**
 * Listens to doctrine postFlush event and updates
 * the urls if the entities are nodetranslations
 */
class NodeTranslationListener
{

    private $nodeTranslations = array();

    /**
     * onFlush doctrine event - collect all nodetranslations in scheduled entity updates here
     *
     * @param OnFlushEventArgs $args
     *
     * Note: only needed because scheduled entity updates are not accessible in postFlush
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();

        // Collect all nodetranslations that are updated
        foreach ($em->getUnitOfWork()->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof NodeTranslation) {
                $this->nodeTranslations[] = $entity;
            }
        }
    }

    /**
     * PostUpdate doctrine event - updates the nodetranslation urls if needed
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $em = $args->getEntityManager();

        foreach ($this->nodeTranslations as $entity) {
            if ($entity instanceof NodeTranslation) {
                $entity = $this->updateUrl($entity);

                if ($entity != false) {
                    $em->persist($entity);
                    $em->flush();

                    $this->updateNodeChildren($entity, $em);
                }
            }
        }
    }

    /**
     * Checks if a nodetranslation has children and update their url
     * @param NodeTranslation $node The node
     * @param EntityManager   $em   The entity manager
     */
    private function updateNodeChildren(NodeTranslation $node, EntityManager $em)
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
     * @param \Kunstmaan\NodeBundle\Entity\NodeTranslation $node
     *
     * @return \Kunstmaan\NodeBundle\Entity\NodeTranslation|bool
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
