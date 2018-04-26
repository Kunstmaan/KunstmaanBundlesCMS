<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeVersionLockHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class NodeHelper
 */
class NodeHelper
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var NodeAdminPublisher */
    private $nodeAdminPublisher;

    /** @var NodeVersionLockHelper */
    private $nodeVersionLockHelper;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var CloneHelper */
    private $cloneHelper;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var [] */
    private $parameters;

    /**
     * NodeHelper constructor.
     *
     * @param EntityManagerInterface   $em
     * @param NodeAdminPublisher       $nodeAdminPublisher
     * @param NodeVersionLockHelper    $nodeVersionLockHelper
     * @param TokenStorageInterface    $tokenStorage
     * @param CloneHelper              $cloneHelper
     * @param EventDispatcherInterface $eventDispatcher
     * @param                          $parameters
     */
    public function __construct(
        EntityManagerInterface $em,
        NodeAdminPublisher $nodeAdminPublisher,
        NodeVersionLockHelper $nodeVersionLockHelper,
        TokenStorageInterface $tokenStorage,
        CloneHelper $cloneHelper,
        EventDispatcherInterface $eventDispatcher,
        $parameters
    ) {
        $this->em = $em;
        $this->nodeAdminPublisher = $nodeAdminPublisher;
        $this->nodeVersionLockHelper = $nodeVersionLockHelper;
        $this->tokenStorage = $tokenStorage;
        $this->cloneHelper = $cloneHelper;
        $this->eventDispatcher = $eventDispatcher;
        $this->parameters = $parameters;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     * @param NodeVersion     $nodeVersion
     *
     * @return array
     */
    public function createNodeVersion(NodeTranslation $nodeTranslation, NodeVersion $nodeVersion)
    {
        $user = $this->em->getRepository('KunstmaanAdminBundle:User')->find(2);
        $nodeVersionIsLocked = $this->nodeVersionLockHelper->isNodeVersionLocked($user, $nodeTranslation, true);

        //Check the version timeout and make a new nodeversion if the timeout is passed
        $thresholdDate = date("Y-m-d H:i:s", time() - $this->parameters['timeout']);
        $updatedDate = date(
            "Y-m-d H:i:s",
            strtotime($nodeVersion->getUpdated()->format("Y-m-d H:i:s"))
        );

        if ($thresholdDate >= $updatedDate || $nodeVersionIsLocked) {
            $page = $nodeVersion->getRef($this->em);
            if ($nodeVersion == $nodeTranslation->getPublicNodeVersion()) {
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

        return [$nodeVersionIsLocked, $nodeVersion];
    }

    /**
     * @param HasNodeInterface $page            The page
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     *
     * @return NodeVersion
     */
    public function createDraftVersion(HasNodeInterface $page, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion)
    {
        $user = $this->em->getRepository('KunstmaanAdminBundle:User')->find(2);
        $publicPage = $this->cloneHelper
            ->deepCloneAndSave($page);

        /* @var NodeVersion $publicNodeVersion */
        $publicNodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')
            ->createNodeVersionFor(
                $publicPage,
                $nodeTranslation,
                $user,
                $nodeVersion->getOrigin(),
                'public',
                $nodeVersion->getCreated()
            );

        $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
        $nodeVersion->setType('draft');
        $nodeVersion->setOrigin($publicNodeVersion);
        $nodeVersion->setCreated(new DateTime());

        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Events::CREATE_DRAFT_VERSION,
            new NodeEvent(
                $nodeTranslation->getNode(),
                $nodeTranslation,
                $nodeVersion,
                $page
            )
        );

        return $nodeVersion;
    }

    /**
     * @param Node             $node
     * @param NodeTranslation  $nodeTranslation
     * @param NodeVersion      $nodeVersion
     * @param HasNodeInterface $page
     * @param boolean          $isStructureNode
     * @param TabPane          $tabPane
     */
    public function updateNode(
        Node $node,
        NodeTranslation $nodeTranslation,
        NodeVersion $nodeVersion,
        HasNodeInterface $page,
        $isStructureNode,
        TabPane $tabPane = null
    ) {
        $this->eventDispatcher->dispatch(
            Events::PRE_PERSIST,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );

        $nodeTranslation->setTitle($page->getTitle());
        if ($isStructureNode) {
            $nodeTranslation->setSlug('');
        }

        $nodeVersion->setUpdated(new \DateTime());
        if ($nodeVersion->getType() == 'public') {
            $nodeTranslation->setUpdated($nodeVersion->getUpdated());
        }
        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->persist($node);
        if (null !== $tabPane) {
            $tabPane->persist($this->em);
        }
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Events::POST_PERSIST,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );
    }

    public function createNodeTranslation()
    {
        // Check with Acl
        //        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $parentNode);

        $parentNodeTranslation = $parentNode->getNodeTranslation($locale, true);
        $parentNodeVersion = $parentNodeTranslation->getPublicNodeVersion();
        $parentPage = $parentNodeVersion->getRef($this->em);

        /** @var HasNodeInterface $newPage */
        $newPage = $apiPage->getPage()->getData();
        $newPage->setParent($parentPage);

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($newPage, $locale, $this->user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation(
            $locale,
            true
        );
        $weight = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')
                ->getMaxChildrenWeight($parentNode, $locale) + 1;
        $nodeTranslation->setWeight($weight);

        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
        }

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        // @TODO update ACL
        //        $this->updateAcl($parentNode, $nodeNewPage);

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        // @TODO
        //        $this->get('event_dispatcher')->dispatch(
        //            Events::ADD_NODE,
        //            new NodeEvent(
        //                $nodeNewPage, $nodeTranslation, $nodeVersion, $newPage
        //            )
        //        );
    }
}
