<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

use Kunstmaan\NodeBundle\Controller\UrlGenerationResult;

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

                $entity = $this->updateUrl($entity, $em);

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
            /* @var Node $child */
            foreach ($children as $child) {
                $translation = $child->getNodeTranslation($node->getLang(), true);
                if ($translation) {
                    $translation = $this->updateUrl($translation, $em);

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
     * @param NodeTranslation $node
     *
     * @return NodeTranslation|bool Returns the node when all is well because it has to be saved.
     */
    private function updateUrl(NodeTranslation $node, $em)
    {
        $result = $this->ensureUniqueUrl($node, $em);
        /*
        if ($result->getSlugModified()) {
            return $node;
        }
        return false;*/
        return $node;
    }



    /**
     * A function that checks the URL and sees if it's unique.
     * It's allowed to be the same when the node is a StructureNode.
     * When a node is deleted it needs to be ignored in the check.
     * Offline nodes need to be included as well.
     *
     * It sluggifies the slug, updates the URL
     * and checks all existing NodeTranslations ([1]), excluding itself. If a URL existsthat has the same url.
     * If an existing one is found the slug is modified, the URL is updated and the check is repeated
     * until no prior urls exist.
     *
     * NOTE: We need a way to tell if the slug has been modified or not.
     * NOTE: Would be cool if we could increment a number after the slug. Like check if it matches -v#
     *       and increment the number.
     *
     * [1] For all languages for now. The issue is that we need a way to know if a node's URL is prepended with the
     * language or not. For now both scenarios are possible so we check for all languages.
     *
     * @var NodeTranslation $translation Reference to the NodeTranslation. This is modified in place.
     * @var boolean $isStructureNode
     * @var string $locale
     *
     * @return UrlGenerationResult Results.
     *
     * @throws
     *
     */
    private function ensureUniqueUrl(NodeTranslation &$translation, $em) {
        $translation->setUrl($translation->getFullSlug());

        /*
        // TEMP!
        $result = new UrlGenerationResult(true, $translation->getSlug(), $translation->getUrl());
        return $result;
        */
        $result = new UrlGenerationResult(false, $translation->getSlug(), $translation->getUrl());


        $pnv = $translation->getPublicNodeVersion();
        var_dump('pnv: ' . $pnv);
        $page = $em->getRepository($pnv->getRefEntityName())->find($pnv->getRefId());
        var_dump('page: ' . $page);
        $isStructureNode = $page->isStructureNode();

        // If it's a structurenode the slug and url should be empty.
        if (($isStructureNode) && (($translation->getSlug() != '' or ($translation->getUrl() != '')))) {
            $result->setSlugModified(true);
            $translation->setSlug('');
            $translation->setUrl('');
            return $result;
        }

        $nodeTranslationRepository = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');

        // Find all translations with this URL, whose nodes are not deleted.
        $translations = $nodeTranslationRepository->getNodeTranslationForUrl($translation->getUrl(), '', false, $translation);

        if (count($translations) > 0) {
            $result->setSlugModified(true);
            $translation->setSlug($this->IncrementString($translation->getSlug()));

            return $this->ensureUniqueUrl($translation, $em);
        }

        return $result;
    }

    /**
     * Increment a string that ends with a number.
     * If the string does not end in a number we'll add the append and then add the first number.
     *
     * @param string $string The string we want to increment.
     * @param string $append The part we want to append before we start adding a number.
     *
     * @return string Incremented string.
     */
    private static function IncrementString($string, $append = '-v')
    {
        $finalDigitGrabberRegex = '/\d+$/';
        $matches = [];

        preg_match($finalDigitGrabberRegex, $string, $matches);

        if (count($matches) > 0) {
            $digit = (int)$matches[0];
            ++$digit;

            // Replace the integer with the new digit.
            return preg_replace($finalDigitGrabberRegex, $digit, $string);
        } else {
            return $string . $append . '1';
        }
    }
}
