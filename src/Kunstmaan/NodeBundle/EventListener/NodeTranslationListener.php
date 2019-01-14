<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class NodeTranslationListener
 * Listens to doctrine postFlush event and updates the urls if the entities are nodetranslations
 */
class NodeTranslationListener
{
    /** @var FlashBagInterface */
    private $flashBag;

    /** @var LoggerInterface */
    private $logger;

    /** @var SlugifierInterface */
    private $slugifier;

    /** @var RequestStack */
    private $requestStack;

    /** @var DomainConfigurationInterface */
    private $domainConfiguration;

    /** @var PagesConfiguration */
    private $pagesConfiguration;

    /**
     * NodeTranslationListener constructor.
     *
     * @param FlashBagInterface            $flashBag
     * @param LoggerInterface              $logger
     * @param SlugifierInterface           $slugifier
     * @param RequestStack                 $requestStack
     * @param DomainConfigurationInterface $domainConfiguration
     * @param PagesConfiguration           $pagesConfiguration
     */
    public function __construct(
        FlashBagInterface $flashBag,
        LoggerInterface $logger,
        SlugifierInterface $slugifier,
        RequestStack $requestStack,
        DomainConfigurationInterface $domainConfiguration,
        PagesConfiguration $pagesConfiguration
    ) {
        $this->flashBag = $flashBag;
        $this->logger = $logger;
        $this->slugifier = $slugifier;
        $this->requestStack = $requestStack;
        $this->domainConfiguration = $domainConfiguration;
        $this->pagesConfiguration = $pagesConfiguration;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof NodeTranslation) {
            $this->setSlugWhenEmpty($entity, $args->getEntityManager());
            $this->ensureSlugIsSlugified($entity);
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
            $this->ensureSlugIsSlugified($entity);
        }
    }

    /**
     * @param NodeTranslation        $nodeTranslation
     * @param EntityManagerInterface $em
     */
    private function setSlugWhenEmpty(NodeTranslation $nodeTranslation, EntityManagerInterface $em)
    {
        $publicNode = $nodeTranslation->getRef($em);

        // Do nothing for StructureNode objects, skip.
        if ($publicNode instanceof HasNodeInterface && $publicNode->isStructureNode()) {
            return;
        }

        // If no slug is set and no structure node, apply title as slug.
        if ($nodeTranslation->getSlug() === null && $nodeTranslation->getNode()->getParent() !== null) {
            $nodeTranslation->setSlug(
                $this->slugifier->slugify($nodeTranslation->getTitle())
            );
        }
    }

    /**
     * @param NodeTranslation $nodeTranslation
     */
    private function ensureSlugIsSlugified(NodeTranslation $nodeTranslation)
    {
        if ($nodeTranslation->getSlug() !== null) {
            $nodeTranslation->setSlug(
                $this->slugifier->slugify($nodeTranslation->getSlug())
            );
        }
    }

    /**
     * OnFlush doctrine event - updates the nodetranslation urls if needed
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        try {
            $em = $args->getEntityManager();

            $class = $em->getClassMetadata(NodeTranslation::class);

            // Collect all nodetranslations that are updated
            foreach ($em->getUnitOfWork()->getScheduledEntityUpdates() as $entity) {
                if ($entity instanceof NodeTranslation) {
                    /** @var Node $publicNode */
                    $publicNode = $entity->getPublicNodeVersion()->getRef($em);

                    // Do nothing for StructureNode objects, skip.
                    if ($publicNode instanceof HasNodeInterface && $publicNode->isStructureNode()) {
                        continue;
                    }

                    $entity = $this->updateUrl($entity, $em);

                    if ($entity !== false) {
                        $em->persist($entity);
                        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($class, $entity);

                        $this->updateNodeChildren($entity, $em, $class);
                    }
                }
            }
        } catch (MappingException $e) {
            // Different entity manager without this entity configured in namespace chain. Ignore
        }
    }

    /**
     * Checks if a nodetranslation has children and update their url
     *
     * @param NodeTranslation        $node  The node
     * @param EntityManagerInterface $em    The entity manager
     * @param ClassMetadata          $class The class meta daat
     */
    private function updateNodeChildren(NodeTranslation $node, EntityManagerInterface $em, ClassMetadata $class)
    {
        $children = $node->getNode()->getChildren();
        if (\count($children) > 0) {
            /* @var Node $child */
            foreach ($children as $child) {
                $translation = $child->getNodeTranslation($node->getLang(), true);
                if ($translation) {
                    $translation = $this->updateUrl($translation, $em);

                    if ($translation !== false) {
                        $em->persist($translation);
                        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($class, $translation);

                        $this->updateNodeChildren($translation, $em, $class);
                    }
                }
            }
        }
    }

    /**
     * Update the url for a nodetranslation
     *
     * @param NodeTranslation        $nodeTranslation The node translation
     * @param EntityManagerInterface $em              The entity manager
     *
     * @return NodeTranslation|bool returns the node when all is well because it has to be saved
     */
    private function updateUrl(NodeTranslation $nodeTranslation, EntityManagerInterface $em)
    {
        $result = $this->ensureUniqueUrl($nodeTranslation, $em);

        if ($result) {
            return $nodeTranslation;
        }

        $this->logger->info(
            sprintf('Found NT %s needed NO change', $nodeTranslation->getId())
        );

        return false;
    }

    /**
     * A function that checks the URL and sees if it's unique.
     * It's allowed to be the same when the node is a StructureNode.
     * When a node is deleted it needs to be ignored in the check.
     * Offline nodes need to be included as well.
     *
     * It sluggifies the slug, updates the URL
     * and checks all existing NodeTranslations ([1]), excluding itself. If a
     * URL existsthat has the same url. If an existing one is found the slug is
     * modified, the URL is updated and the check is repeated until no prior
     * urls exist.
     *
     * NOTE: We need a way to tell if the slug has been modified or not.
     * NOTE: Would be cool if we could increment a number after the slug. Like
     * check if it matches -v# and increment the number.
     *
     * [1] For all languages for now. The issue is that we need a way to know
     * if a node's URL is prepended with the language or not. For now both
     * scenarios are possible so we check for all languages.
     *
     * @param NodeTranslation        $translation Reference to the NodeTranslation.
     *                                            This is modified in place.
     * @param EntityManagerInterface $em          The entity manager
     * @param array                  $flashes     The flash messages array
     *
     * @return bool
     */
    private function ensureUniqueUrl(NodeTranslation $translation, EntityManagerInterface $em, array $flashes = [])
    {
        // Can't use GetRef here yet since the NodeVersions aren't loaded yet for some reason.
        $nodeVersion = $translation->getPublicNodeVersion();
        $page = $em->getRepository($nodeVersion->getRefEntityName())
            ->find($nodeVersion->getRefId());

        if (null === $page) {
            return false;
        }

        $isStructureNode = $page->isStructureNode();

        // If it's a StructureNode the slug and url should be empty.
        if ($isStructureNode) {
            $translation->setSlug('');
            $translation->setUrl($translation->getFullSlug());

            return true;
        }

        /* @var NodeTranslationRepository $nodeTranslationRepository */
        $nodeTranslationRepository = $em->getRepository(NodeTranslation::class);

        if ($translation->getUrl() === $translation->getFullSlug()) {
            $this->logger->debug(
                sprintf(
                    'Evaluating URL for NT %s getUrl: "%s" getFullSlug: "%s"',
                    $translation->getId(),
                    $translation->getUrl(),
                    $translation->getFullSlug()
                )
            );

            return false;
        }

        // Adjust the URL.
        $translation->setUrl($translation->getFullSlug());

        // Find all translations with this new URL, whose nodes are not deleted.
        $translations = $nodeTranslationRepository->getAllNodeTranslationsForUrl(
            $translation->getUrl(),
            $translation->getLang(),
            false,
            $translation,
            $this->domainConfiguration->getRootNode()
        );

        $this->logger->debug(
            sprintf(
                'Found %s node(s) that math url "%s"',
                \count($translations),
                $translation->getUrl()
            )
        );

        $translationsWithSameUrl = [];

        /** @var NodeTranslation $trans */
        foreach ($translations as $trans) {
            if (!$this->pagesConfiguration->isStructureNode($trans->getPublicNodeVersion()->getRefEntityName())) {
                $translationsWithSameUrl[] = $trans;
            }
        }

        if (\count($translationsWithSameUrl) > 0) {
            $oldUrl = $translation->getFullSlug();
            $translation->setSlug(
                $this->slugifier->slugify(
                    $this->incrementString($translation->getSlug())
                )
            );
            $newUrl = $translation->getFullSlug();

            $message = sprintf(
                'The URL of the page has been changed from %s to %s since another page already uses this URL',
                $oldUrl,
                $newUrl
            );
            $this->logger->info($message);
            $flashes[] = $message;

            $this->ensureUniqueUrl($translation, $em, $flashes);
        } elseif (\count($flashes) > 0 && $this->isInRequestScope()) {
            // No translations found so we're certain we can show this message.
            $flash = current(\array_slice($flashes, -1));
            $this->flashBag->add(FlashTypes::WARNING, $flash);
        }

        return true;
    }

    /**
     * Increment a string that ends with a number.
     * If the string does not end in a number we'll add the append and then add
     * the first number.
     *
     * @param string $string the string we want to increment
     * @param string $append the part we want to append before we start adding
     *                       a number
     *
     * @return string incremented string
     */
    private function incrementString($string, $append = '-v')
    {
        $finalDigitGrabberRegex = '/\d+$/';
        $matches = [];

        preg_match($finalDigitGrabberRegex, $string, $matches);

        if (\count($matches) > 0) {
            $digit = (int) $matches[0];
            ++$digit;

            // Replace the integer with the new digit.
            return preg_replace($finalDigitGrabberRegex, $digit, $string);
        }

        return $string.$append.'1';
    }

    /**
     * @return bool
     */
    private function isInRequestScope()
    {
        return $this->requestStack && $this->requestStack->getCurrentRequest();
    }
}
