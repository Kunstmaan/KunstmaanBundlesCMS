<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Event\OnFlushEventArgs,
    Doctrine\ORM\Event\PostFlushEventArgs;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Kunstmaan\NodeBundle\Entity\Node,
    Kunstmaan\NodeBundle\Entity\NodeTranslation;

use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Listens to doctrine postFlush event and updates
 * the urls if the entities are nodetranslations
 */
class NodeTranslationListener
{

    private $session;
    private $logger;
    private $nodeTranslations;

    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param Session $session The session
     * @param Logger  $logger  The logger
     */
    public function __construct(Session $session, $logger, SlugifierInterface $slugifier)
    {
        $this->nodeTranslations = array();
        $this->session = $session;
        $this->logger = $logger;
        $this->slugifier = $slugifier;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof NodeTranslation) {
            $this->setSlugWhenEmpty($entity, $args->getEntityManager());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof NodeTranslation) {
            $this->setSlugWhenEmpty($entity, $args->getEntityManager());
        }
    }

    private function setSlugWhenEmpty(NodeTranslation $nodeTranslation, EntityManager $em)
    {
        $publicNode = $nodeTranslation->getRef($em);

        /** Do nothing for StructureNode objects, skip */
        if ($publicNode instanceof HasNodeInterface && $publicNode->isStructureNode()) {
            return;
        }

        /**
         * If no slug is set and no structure node, apply title as slug
         */
        if ($nodeTranslation->getSlug() == null && $nodeTranslation->getNode()->getParent() != null) {
            $nodeTranslation->setSlug($this->slugifier->slugify($nodeTranslation->getTitle()));
        }
    }


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
            /** @var $entity NodeTranslation */
            if ($entity instanceof NodeTranslation) {
                $publicNodeVersion = $entity->getPublicNodeVersion();

                /** @var $publicNodeVersion NodeVersion */
                $publicNode = $publicNodeVersion->getRef($em);

                /** Do nothing for StructureNode objects, skip */
                if ($publicNode instanceof HasNodeInterface && $publicNode->isStructureNode()) {
                    continue;
                }

                $entity = $this->updateUrl($entity, $em);

                if ($entity !== false) {
                    $em->persist($entity);
                    $em->flush($entity);

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

                    if ($translation !== false) {
                        $em->persist($translation);
                        $em->flush($translation);

                        $this->updateNodeChildren($translation, $em);
                    }
                }
            }
        }
    }

    /**
     * Update the url for a nodetranslation
     * @param NodeTranslation $nodeTranslation The node translation
     * @param EntityManager   $em              The entity manager
     *
     * @return NodeTranslation|bool Returns the node when all is well because it has to be saved.
     */
    private function updateUrl(NodeTranslation $nodeTranslation, $em)
    {
        $result = $this->ensureUniqueUrl($nodeTranslation, $em);

        if ($result) {
            return $nodeTranslation;
        }

        $this->logger->addInfo('Found NT ' . $nodeTranslation->getId() . ' needed NO change');

        return false;
    }

    /**
     * @param NodeTranslation $translation The node translation
     * @param EntityManager   $em          The entity manager
     * @param array           $flashes     Flashes
     *
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
     * @param NodeTranslation &$translation Reference to the NodeTranslation. This is modified in place.
     * @param EntityManager   $em           The entity manager
     * @param array           $flashes      The flash messages array
     *
     * @return bool
     *
     * @return boolean
     */
    private function ensureUniqueUrl(NodeTranslation &$translation, EntityManager $em, $flashes = array())
    {
        // Can't use GetRef here yet since the NodeVersions aren't loaded yet for some reason.
        $pnv = $translation->getPublicNodeVersion();

        $page = $em->getRepository($pnv->getRefEntityName())->find($pnv->getRefId());
        $isStructureNode = $page->isStructureNode();

        // If it's a StructureNode the slug and url should be empty.
        if (($isStructureNode)) {
            $translation->setSlug('');
            $translation->setUrl($translation->getFullSlug());

            return true;
        }

        /* @var Kunstmaan\NodeBundle\Entity\NodeTranslation $nodeTranslationRepository */
        $nodeTranslationRepository = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');

        if (($translation->getUrl() == $translation->getFullSlug())) {
            $this->logger->addDebug('Evaluating URL for NT ' . $translation->getId() . ' getUrl: \'' . $translation->getUrl() . '\' getFullSlug: \'' . $translation->getFullSlug() . '\'');

            return false;
        }

        // Adjust the URL.
        $translation->setUrl($translation->getFullSlug());

        // Find all translations with this new URL, whose nodes are not deleted.
        $translations = $nodeTranslationRepository->getNodeTranslationForUrl($translation->getUrl(), $translation->getLang(), false, $translation);

        $this->logger->addDebug('Found ' . count($translations) . ' node(s) that match url \'' . $translation->getUrl() . '\'');

        if (count($translations) > 0) {
            $oldUrl = $translation->getFullSlug();
            $translation->setSlug($this->slugifier->slugify($this->IncrementString($translation->getSlug())));
            $newUrl = $translation->getFullSlug();

            $message = 'The URL of the page has been changed from ' . $oldUrl . ' to ' . $newUrl . ' since another page already uses this URL.';
            $this->logger->addInfo($message);
            $flashes[] = $message;

            $this->ensureUniqueUrl($translation, $em, $flashes);
        } elseif (count($flashes) > 0) {
            // No translations found so we're certain we can show this message.
            $flash = end($flashes);
            $flash = current(array_slice($flashes, -1));
            $this->session->getFlashBag()->add('warning', $flash);
        }

        return true;
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
        $matches = array();

        preg_match($finalDigitGrabberRegex, $string, $matches);

        if (count($matches) > 0) {
            $digit = (int) $matches[0];
            ++$digit;

            // Replace the integer with the new digit.
            return preg_replace($finalDigitGrabberRegex, $digit, $string);
        } else {
            return $string . $append . '1';
        }
    }
}
