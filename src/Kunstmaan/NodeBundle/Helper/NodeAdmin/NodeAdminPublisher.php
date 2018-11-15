<?php

namespace Kunstmaan\NodeBundle\Helper\NodeAdmin;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;

class NodeAdminPublisher
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CloneHelper
     */
    private $cloneHelper;

    /**
     * @param EntityManager                 $em                   The entity manager
     * @param TokenStorageInterface         $tokenStorage         The security token storage
     * @param AuthorizationCheckerInterface $authorizationChecker The security authorization checker
     * @param EventDispatcherInterface      $eventDispatcher      The Event dispatcher
     * @param CloneHelper                   $cloneHelper          The clone helper
     */
    public function __construct(
        EntityManager $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        CloneHelper $cloneHelper
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->cloneHelper = $cloneHelper;
    }

    /**
     * If there is a draft version it'll try to publish the draft first. Makse snese because if you want to publish the public version you don't publish but you save.
     *
     * @param NodeTranslation $nodeTranslation
     * @param null|BaseUser   $user
     *
     *  @throws AccessDeniedException
     */
    public function publish(NodeTranslation $nodeTranslation, $user = null)
    {
        if (false === $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_PUBLISH, $nodeTranslation->getNode())) {
            throw new AccessDeniedException();
        }

        if (is_null($user)) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $node = $nodeTranslation->getNode();

        $nodeVersion = $nodeTranslation->getNodeVersion('draft');
        if (!is_null($nodeVersion)) {
            $page = $nodeVersion->getRef($this->em);
            /** @var $nodeVersion NodeVersion */
            $nodeVersion = $this->createPublicVersion($page, $nodeTranslation, $nodeVersion, $user);
            $nodeTranslation = $nodeVersion->getNodeTranslation();
        } else {
            $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        }

        $page = $nodeVersion->getRef($this->em);

        $this->eventDispatcher->dispatch(
            Events::PRE_PUBLISH,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );
        $nodeTranslation
            ->setOnline(true)
            ->setPublicNodeVersion($nodeVersion)
            ->setUpdated(new \DateTime());
        $this->em->persist($nodeTranslation);
        $this->em->flush();

        // Remove scheduled task
        $this->unSchedulePublish($nodeTranslation);

        $this->eventDispatcher->dispatch(
            Events::POST_PUBLISH,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );
    }

    /**
     * @param NodeTranslation $nodeTranslation The NodeTranslation
     * @param \DateTime       $date            The date to publish
     *
     * @throws AccessDeniedException
     */
    public function publishLater(NodeTranslation $nodeTranslation, \DateTime $date)
    {
        $node = $nodeTranslation->getNode();
        if (false === $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        //remove existing first
        $this->unSchedulePublish($nodeTranslation);

        $user = $this->tokenStorage->getToken()->getUser();
        $queuedNodeTranslationAction = new QueuedNodeTranslationAction();
        $queuedNodeTranslationAction
            ->setNodeTranslation($nodeTranslation)
            ->setAction(QueuedNodeTranslationAction::ACTION_PUBLISH)
            ->setUser($user)
            ->setDate($date);
        $this->em->persist($queuedNodeTranslationAction);
        $this->em->flush();
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @throws AccessDeniedException
     */
    public function unPublish(NodeTranslation $nodeTranslation)
    {
        if (false === $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $nodeTranslation->getNode())) {
            throw new AccessDeniedException();
        }

        $node = $nodeTranslation->getNode();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->eventDispatcher->dispatch(
            Events::PRE_UNPUBLISH,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );
        $nodeTranslation->setOnline(false);
        $this->em->persist($nodeTranslation);
        $this->em->flush();

        // Remove scheduled task
        $this->unSchedulePublish($nodeTranslation);

        $this->eventDispatcher->dispatch(
            Events::POST_UNPUBLISH,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );
    }

    /**
     * @param NodeTranslation $nodeTranslation The NodeTranslation
     * @param \DateTime       $date            The date to unpublish
     *
     * @throws AccessDeniedException
     */
    public function unPublishLater(NodeTranslation $nodeTranslation, \DateTime $date)
    {
        $node = $nodeTranslation->getNode();
        if (false === $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        //remove existing first
        $this->unSchedulePublish($nodeTranslation);
        $user = $this->tokenStorage->getToken()->getUser();
        $queuedNodeTranslationAction = new QueuedNodeTranslationAction();
        $queuedNodeTranslationAction
            ->setNodeTranslation($nodeTranslation)
            ->setAction(QueuedNodeTranslationAction::ACTION_UNPUBLISH)
            ->setUser($user)
            ->setDate($date);
        $this->em->persist($queuedNodeTranslationAction);
        $this->em->flush();
    }

    /**
     * @param NodeTranslation $nodeTranslation
     */
    public function unSchedulePublish(NodeTranslation $nodeTranslation)
    {
        /* @var Node $node */
        $queuedNodeTranslationAction = $this->em->getRepository('KunstmaanNodeBundle:QueuedNodeTranslationAction')
            ->findOneBy(array('nodeTranslation' => $nodeTranslation));

        if (!is_null($queuedNodeTranslationAction)) {
            $this->em->remove($queuedNodeTranslationAction);
            $this->em->flush();
        }
    }

    /**
     * This shouldn't be here either but it's an improvement.
     *
     * @param HasNodeInterface $page            The page
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     * @param BaseUser         $user            The user
     *
     * @return mixed
     */
    public function createPublicVersion(
        HasNodeInterface $page,
        NodeTranslation $nodeTranslation,
        NodeVersion $nodeVersion,
        BaseUser $user
    ) {
        $newPublicPage = $this->cloneHelper->deepCloneAndSave($page);
        $newNodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor(
            $newPublicPage,
            $nodeTranslation,
            $user,
            $nodeVersion
        );

        $newNodeVersion
            ->setOwner($nodeVersion->getOwner())
            ->setUpdated($nodeVersion->getUpdated())
            ->setCreated($nodeVersion->getCreated());

        $nodeVersion
            ->setOwner($user)
            ->setCreated(new \DateTime())
            ->setOrigin($newNodeVersion);

        $this->em->persist($newNodeVersion);
        $this->em->persist($nodeVersion);
        $this->em->persist($nodeTranslation);
        $this->em->flush();
        $this->eventDispatcher->dispatch(
            Events::CREATE_PUBLIC_VERSION,
            new NodeEvent($nodeTranslation->getNode(), $nodeTranslation, $nodeVersion, $newPublicPage)
        );

        return $newNodeVersion;
    }

    /**
     * @param Request         $request
     * @param NodeTranslation $nodeTranslation
     */
    public function chooseHowToPublish(Request $request, NodeTranslation $nodeTranslation, TranslatorInterface $translator)
    {
        /** @var Session $session */
        $session = $request->getSession();

        if ($request->request->has('publish_later') && $request->get('pub_date')) {
            $date = new \DateTime(
                $request->get('pub_date') . ' ' . $request->get('pub_time')
            );
            $this->publishLater($nodeTranslation, $date);
            $session->getFlashBag()->add(
                FlashTypes::SUCCESS,
                $translator->trans('kuma_node.admin.publish.flash.success_scheduled')
            );
        } else {
            $this->publish($nodeTranslation);
            $session->getFlashBag()->add(
                FlashTypes::SUCCESS,
                $translator->trans('kuma_node.admin.publish.flash.success_published')
            );
        }
    }

    /**
     * @param Request         $request
     * @param NodeTranslation $nodeTranslation
     */
    public function chooseHowToUnpublish(Request $request, NodeTranslation $nodeTranslation, TranslatorInterface $translator)
    {
        /** @var Session $session */
        $session = $request->getSession();

        if ($request->request->has('unpublish_later') && $request->get('unpub_date')) {
            $date = new \DateTime($request->get('unpub_date') . ' ' . $request->get('unpub_time'));
            $this->unPublishLater($nodeTranslation, $date);
            $session->getFlashBag()->add(
                FlashTypes::SUCCESS,
                $translator->trans('kuma_node.admin.unpublish.flash.success_scheduled')
            );
        } else {
            $this->unPublish($nodeTranslation);
            $session->getFlashBag()->add(
                FlashTypes::SUCCESS,
                $translator->trans('kuma_node.admin.unpublish.flash.success_unpublished')
            );
        }
    }
}
