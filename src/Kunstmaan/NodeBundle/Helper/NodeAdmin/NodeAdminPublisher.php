<?php

namespace Kunstmaan\NodeBundle\Helper\NodeAdmin;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;

/**
 * NodeAdminPublisher
 */
class NodeAdminPublisher
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManager            $em              The entity manager
     * @param SecurityContextInterface $securityContext The security context
     * @param EventDispatcherInterface $eventDispatcher The Event dispatcher
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @throws AccessDeniedException
     */
    public function publish(NodeTranslation $nodeTranslation)
    {
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_PUBLISH, $nodeTranslation->getNode())) {
            throw new AccessDeniedException();
        }

        $node = $nodeTranslation->getNode();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->eventDispatcher->dispatch(Events::PRE_PUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeTranslation->setOnline(true);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->eventDispatcher->dispatch(Events::POST_PUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));
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
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        //remove existing first
        $this->unSchedulePublish($nodeTranslation);

        $user = $this->securityContext->getToken()->getUser();
        $queuedNodeTranslationAction = new QueuedNodeTranslationAction();
        $queuedNodeTranslationAction
           ->setNodeTranslation($nodeTranslation)
           ->setAction(QueuedNodeTranslationAction::ACTION_PUBLISH)
           ->setUserId($user->getId())
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
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $nodeTranslation->getNode())) {
            throw new AccessDeniedException();
        }

        $node = $nodeTranslation->getNode();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->eventDispatcher->dispatch(Events::PRE_UNPUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeTranslation->setOnline(false);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->eventDispatcher->dispatch(Events::POST_UNPUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));
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
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        //remove existing first
        $this->unSchedulePublish($nodeTranslation);

        $user = $this->securityContext->getToken()->getUser();
        $queuedNodeTranslationAction = new QueuedNodeTranslationAction();
        $queuedNodeTranslationAction
        ->setNodeTranslation($nodeTranslation)
        ->setAction(QueuedNodeTranslationAction::ACTION_UNPUBLISH)
        ->setUserId($user->getId())
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
        $queuedNodeTranslationActions = $this->em->getRepository('KunstmaanNodeBundle:QueuedNodeTranslationAction')->findAll(array('nodeTranslation' => $nodeTranslation->getId()));

        foreach ($queuedNodeTranslationActions as $queuedNodeTranslationAction) {
            $this->em->remove($queuedNodeTranslationAction);
            $this->em->flush();
        }
    }
}
