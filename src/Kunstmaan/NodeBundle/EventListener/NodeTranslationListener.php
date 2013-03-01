<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Event\OnFlushEventArgs,
    Doctrine\ORM\Event\PostFlushEventArgs;

use Kunstmaan\NodeBundle\Entity\Node,
    Kunstmaan\NodeBundle\Entity\NodeTranslation;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Listens to doctrine postFlush event and updates
 * the urls if the entities are nodetranslations
 */
class NodeTranslationListener
{

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

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
    private function updateUrl(NodeTranslation $translation, $em)
    {
        if ($translation->getUrl() != $translation->getFullSlug())
        {
            $this->ensureUniqueUrl($translation, $em);
            return $translation;
        } else {
            return false;
        }
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
     *
     */
    private function ensureUniqueUrl(NodeTranslation &$translation, $em, $flashes = []) {
        $translation->setUrl($translation->getFullSlug());

        // Can't use GetRef here yet since the NodeVersions aren't loaded yet for some reason.
        $pnv = $translation->getPublicNodeVersion();

        $page = $em->getRepository($pnv->getRefEntityName())->find($pnv->getRefId());
        $isStructureNode = $page->isStructureNode();

        // If it's a StructureNode the slug and url should be empty.
        if (($isStructureNode)) {
            $translation->setSlug('');
            $translation->setUrl($translation->getFullSlug());
            return null;
        }

        $nodeTranslationRepository = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');

        // Find all translations with this URL, whose nodes are not deleted.
        $translations = $nodeTranslationRepository->getNodeTranslationForUrl($translation->getUrl(), '', false, $translation);

        if (count($translations) > 0) {
            $oldUrl = $translation->getFullSlug();
            $translation->setSlug($this->IncrementString($translation->getSlug()));
            $newUrl = $translation->getFullSlug();

            $flashes[] = 'The URL of the page has been changed from ' . $oldUrl . ' to ' . $newUrl . ' since another page already uses this URL.';

            $this->ensureUniqueUrl($translation, $em, $flashes);
        } elseif (count($flashes) > 0) {
            $flash = end($flashes);
            $flash = current(array_slice($flashes, -1));
            $this->session->getFlashBag()->add('warning', $flash);
        }

        return null;
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
