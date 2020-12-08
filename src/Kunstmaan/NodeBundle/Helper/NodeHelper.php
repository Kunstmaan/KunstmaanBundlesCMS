<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Event\RecopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NodeHelper
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var NodeAdminPublisher */
    private $nodeAdminPublisher;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var CloneHelper */
    private $cloneHelper;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        EntityManagerInterface $em,
        NodeAdminPublisher $nodeAdminPublisher,
        TokenStorageInterface $tokenStorage,
        CloneHelper $cloneHelper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->nodeAdminPublisher = $nodeAdminPublisher;
        $this->tokenStorage = $tokenStorage;
        $this->cloneHelper = $cloneHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param HasNodeInterface $page            The page
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     *
     * @return NodeVersion
     */
    public function createDraftVersion(
        HasNodeInterface $page,
        NodeTranslation $nodeTranslation,
        NodeVersion $nodeVersion
    ) {
        $user = $this->getAdminUser();
        $publicPage = $this->cloneHelper->deepCloneAndSave($page);

        /* @var NodeVersion $publicNodeVersion */
        $publicNodeVersion = $this->em->getRepository(NodeVersion::class)
            ->createNodeVersionFor(
                $publicPage,
                $nodeTranslation,
                $user,
                $nodeVersion->getOrigin(),
                NodeVersion::PUBLIC_VERSION,
                $nodeVersion->getCreated()
            );

        $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
        $nodeVersion->setType(NodeVersion::DRAFT_VERSION);
        $nodeVersion->setOrigin($publicNodeVersion);
        $nodeVersion->setCreated(new \DateTime());

        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->flush();

        $this->dispatch(
            new NodeEvent(
                $nodeTranslation->getNode(),
                $nodeTranslation,
                $nodeVersion,
                $page
            ),
            Events::CREATE_DRAFT_VERSION
        );

        return $nodeVersion;
    }

    /**
     * @param int  $nodeVersionTimeout
     * @param bool $nodeVersionIsLocked
     */
    public function prepareNodeVersion(NodeVersion $nodeVersion, NodeTranslation $nodeTranslation, $nodeVersionTimeout, $nodeVersionIsLocked)
    {
        $user = $this->getAdminUser();
        $thresholdDate = date('Y-m-d H:i:s', time() - $nodeVersionTimeout);
        $updatedDate = date('Y-m-d H:i:s', strtotime($nodeVersion->getUpdated()->format('Y-m-d H:i:s')));

        if ($thresholdDate >= $updatedDate || $nodeVersionIsLocked) {
            $page = $nodeVersion->getRef($this->em);
            if ($nodeVersion === $nodeTranslation->getPublicNodeVersion()) {
                $this->nodeAdminPublisher
                    ->createPublicVersion(
                        $page,
                        $nodeTranslation,
                        $nodeVersion,
                        $user
                    );
            } else {
                $this->createDraftVersion(
                    $page,
                    $nodeTranslation,
                    $nodeVersion
                );
            }
        }
    }

    /**
     * @param bool    $isStructureNode
     * @param TabPane $tabPane
     *
     * @return NodeTranslation
     */
    public function updatePage(
        Node $node,
        NodeTranslation $nodeTranslation,
        NodeVersion $nodeVersion,
        HasNodeInterface $page,
        $isStructureNode,
        TabPane $tabPane = null
    ) {
        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
            Events::PRE_PERSIST
        );

        $nodeTranslation->setTitle($page->getTitle());
        if ($isStructureNode) {
            $nodeTranslation->setSlug('');
        }

        $nodeVersion->setUpdated(new \DateTime());
        if ($nodeVersion->getType() == NodeVersion::PUBLIC_VERSION) {
            $nodeTranslation->setUpdated($nodeVersion->getUpdated());
        }
        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->persist($node);
        if (null !== $tabPane) {
            $tabPane->persist($this->em);
        }
        $this->em->flush();

        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
            Events::POST_PERSIST
        );

        return $nodeTranslation;
    }

    /**
     * @param string $refEntityType
     * @param string $pageTitle
     * @param string $locale
     *
     * @return NodeTranslation
     */
    public function createPage(
        $refEntityType,
        $pageTitle,
        $locale,
        Node $parentNode = null)
    {
        $user = $this->getAdminUser();

        $newPage = $this->createNewPage($refEntityType, $pageTitle);
        if (null !== $parentNode) {
            $parentNodeTranslation = $parentNode->getNodeTranslation($locale, true);
            $parentNodeVersion = $parentNodeTranslation->getPublicNodeVersion();
            $parentPage = $parentNodeVersion->getRef($this->em);
            $newPage->setParent($parentPage);
        }

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository(Node::class)->createNodeFor($newPage, $locale, $user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation($locale, true);
        if (null !== $parentNode) {
            $weight = $this->em->getRepository(NodeTranslation::class)->getMaxChildrenWeight(
                    $parentNode,
                    $locale
                ) + 1;
            $nodeTranslation->setWeight($weight);
        }

        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
        }

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new NodeEvent($nodeNewPage, $nodeTranslation, $nodeVersion, $newPage),
            Events::ADD_NODE
        );

        return $nodeTranslation;
    }

    /**
     * @param string $locale
     *
     * @return NodeTranslation
     */
    public function deletePage(Node $node, $locale)
    {
        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
            Events::PRE_DELETE
        );

        $node->setDeleted(true);
        $this->em->persist($node);

        $this->deleteNodeChildren($node, $locale);
        $this->em->flush();

        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
            Events::POST_DELETE
        );

        return $nodeTranslation;
    }

    /**
     * @param string $locale
     *
     * @return HasNodeInterface
     */
    public function getPageWithNodeInterface(Node $node, $locale)
    {
        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        return $nodeVersion->getRef($this->em);
    }

    /**
     * @param string $sourceLocale
     * @param string $locale
     *
     * @return NodeTranslation
     */
    public function copyPageFromOtherLanguage(Node $node, $sourceLocale, $locale)
    {
        $user = $this->getAdminUser();

        $sourceNodeTranslation = $node->getNodeTranslation($sourceLocale, true);
        $sourceNodeVersion = $sourceNodeTranslation->getPublicNodeVersion();
        $sourcePage = $sourceNodeVersion->getRef($this->em);
        $targetPage = $this->cloneHelper->deepCloneAndSave($sourcePage);

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)->createNodeTranslationFor($targetPage, $locale, $node, $user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new CopyPageTranslationNodeEvent(
                $node,
                $nodeTranslation,
                $nodeVersion,
                $targetPage,
                $sourceNodeTranslation,
                $sourceNodeVersion,
                $sourcePage,
                $sourceLocale
            ),
            Events::COPY_PAGE_TRANSLATION
        );

        return $nodeTranslation;
    }

    /**
     * @param string $locale
     * @param string $title
     *
     * @return NodeTranslation|null
     */
    public function duplicatePage(Node $node, $locale, $title = 'New page')
    {
        $user = $this->getAdminUser();

        $sourceNodeTranslations = $node->getNodeTranslation($locale, true);
        $sourcePage = $sourceNodeTranslations->getPublicNodeVersion()->getRef($this->em);
        $targetPage = $this->cloneHelper->deepCloneAndSave($sourcePage);
        $targetPage->setTitle($title);

        if ($node->getParent()) {
            $parentNodeTranslation = $node->getParent()->getNodeTranslation($locale, true);
            $parent = $parentNodeTranslation->getPublicNodeVersion()->getRef($this->em);
            $targetPage->setParent($parent);
        }
        $this->em->persist($targetPage);
        $this->em->flush();

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository(Node::class)->createNodeFor($targetPage, $locale, $user);

        $nodeTranslation = $nodeNewPage->getNodeTranslation($locale, true);
        if ($targetPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
            $this->em->persist($nodeTranslation);
        }
        $this->em->flush();

        return $nodeTranslation;
    }

    /**
     * @param int    $sourceNodeTranslationId
     * @param string $locale
     *
     * @return NodeTranslation
     */
    public function createPageDraftFromOtherLanguage(Node $node, $sourceNodeTranslationId, $locale)
    {
        $user = $this->getAdminUser();

        $sourceNodeTranslation = $this->em->getRepository(NodeTranslation::class)->find($sourceNodeTranslationId);
        $sourceNodeVersion = $sourceNodeTranslation->getPublicNodeVersion();
        $sourcePage = $sourceNodeVersion->getRef($this->em);
        $targetPage = $this->cloneHelper->deepCloneAndSave($sourcePage);

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)->addDraftNodeVersionFor($targetPage, $locale, $node, $user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new RecopyPageTranslationNodeEvent(
                $node,
                $nodeTranslation,
                $nodeVersion,
                $targetPage,
                $sourceNodeTranslation,
                $sourceNodeVersion,
                $sourcePage,
                $sourceNodeTranslation->getLang()
            ),
            Events::RECOPY_PAGE_TRANSLATION
        );

        return $nodeTranslation;
    }

    /**
     * @param string $locale
     *
     * @return NodeTranslation
     */
    public function createEmptyPage(Node $node, $locale)
    {
        $user = $this->getAdminUser();

        $refEntityName = $node->getRefEntityName();
        $targetPage = $this->createNewPage($refEntityName);

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)->createNodeTranslationFor($targetPage, $locale, $node, $user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $targetPage),
            Events::ADD_EMPTY_PAGE_TRANSLATION
        );

        return $nodeTranslation;
    }

    /**
     * @param string $entityType
     * @param string $title
     *
     * @return HasNodeInterface
     */
    protected function createNewPage($entityType, $title = 'No title')
    {
        /* @var HasNodeInterface $newPage */
        $newPage = new $entityType();
        $newPage->setTitle($title);

        $this->em->persist($newPage);
        $this->em->flush();

        return $newPage;
    }

    /**
     * @param string $locale
     */
    protected function deleteNodeChildren(Node $node, $locale)
    {
        $children = $node->getChildren();

        /* @var Node $childNode */
        foreach ($children as $childNode) {
            $childNodeTranslation = $childNode->getNodeTranslation($locale, true);
            $childNodeVersion = $childNodeTranslation->getPublicNodeVersion();
            $childNodePage = $childNodeVersion->getRef($this->em);

            $this->dispatch(
                new NodeEvent(
                    $childNode,
                    $childNodeTranslation,
                    $childNodeVersion,
                    $childNodePage
                ),
                Events::PRE_DELETE
            );

            $childNode->setDeleted(true);
            $this->em->persist($childNode);

            $this->deleteNodeChildren($childNode, $locale);

            $this->dispatch(
                new NodeEvent(
                    $childNode,
                    $childNodeTranslation,
                    $childNodeVersion,
                    $childNodePage
                ),
                Events::POST_DELETE
            );
        }
    }

    /**
     * @return mixed|null
     */
    protected function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if ($token) {
            $user = $token->getUser();
            if ($user && $user !== 'anon.' && $user instanceof User) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getAdminUser()
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('Access denied: User should be an admin user');
        }

        return $user;
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($this->eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
