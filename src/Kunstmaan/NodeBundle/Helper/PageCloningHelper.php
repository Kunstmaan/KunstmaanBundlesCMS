<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\DuplicateSubPageInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\PostNodeDuplicateEvent;
use Kunstmaan\NodeBundle\Event\PreNodeDuplicateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PageCloningHelper
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CloneHelper */
    private $cloneHelper;

    /** @var AclProviderInterface */
    private $aclProvider;

    /** @var ObjectIdentityRetrievalStrategyInterface */
    private $identityRetrievalStrategy;

    /** @var AuthorizationCheckerInterface */
    private $authorizationCheckerInterface;

    /** @var EventDispatcherInterface */
    private $eventDispatcherInterface;

    public function __construct(EntityManagerInterface $em, CloneHelper $cloneHelper, AclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $identityRetrivalStrategy, AuthorizationCheckerInterface $authorizationChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->cloneHelper = $cloneHelper;
        $this->aclProvider = $aclProvider;
        $this->identityRetrievalStrategy = $identityRetrivalStrategy;
        $this->authorizationCheckerInterface = $authorizationChecker;
        $this->eventDispatcherInterface = $eventDispatcher;
    }

    /**
     * @throws AccessDeniedException
     */
    public function duplicateWithChildren($id, string $locale, BaseUser $user, string $title = null): Node
    {
        /* @var Node $parentNode */
        $originalNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $originalNode);

        $this->dispatch(new PreNodeDuplicateEvent($originalNode), Events::PRE_DUPLICATE_WITH_CHILDREN);

        $newPage = $this->clonePage($originalNode, $locale, $title);
        $nodeNewPage = $this->createNodeStructureForNewPage($originalNode, $newPage, $user, $locale);

        $this->dispatch(new PostNodeDuplicateEvent($originalNode, $nodeNewPage, $newPage), Events::POST_DUPLICATE_WITH_CHILDREN);

        $this->cloneChildren($originalNode, $newPage, $user, $locale);

        return $nodeNewPage;
    }

    private function denyAccessUnlessGranted($attributes, $subject = null, $message = 'Access Denied.')
    {
        if (!$this->authorizationCheckerInterface->isGranted($attributes, $subject)) {
            $exception = new AccessDeniedException();
            $exception->setAttributes($attributes);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    public function clonePage(Node $originalNode, $locale, $title = null)
    {
        $originalNodeTranslations = $originalNode->getNodeTranslation($locale, true);
        $originalRef = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);

        $newPage = $this->cloneHelper->deepCloneAndSave($originalRef);

        if ($title !== null) {
            $newPage->setTitle($title);
        }

        //set the parent
        $parentNodeTranslation = $originalNode->getParent()->getNodeTranslation($locale, true);
        $parent = $parentNodeTranslation->getPublicNodeVersion()->getRef($this->em);
        $newPage->setParent($parent);

        $this->em->persist($newPage);
        $this->em->flush();

        return $newPage;
    }

    private function createNodeStructureForNewPage(Node $originalNode, HasNodeInterface $newPage, BaseUser $user, string $locale): Node
    {
        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($newPage, $locale, $user);

        if ($newPage->isStructureNode()) {
            $nodeTranslation = $nodeNewPage->getNodeTranslation($locale, true);
            $nodeTranslation->setSlug('');
            $this->em->persist($nodeTranslation);
        }
        $this->em->flush();

        $this->updateAcl($originalNode, $nodeNewPage);

        return $nodeNewPage;
    }

    private function updateAcl($originalNode, $nodeNewPage): void
    {
        $originalIdentity = $this->identityRetrievalStrategy->getObjectIdentity($originalNode);
        $originalAcl = $this->aclProvider->findAcl($originalIdentity);

        $newIdentity = $this->identityRetrievalStrategy->getObjectIdentity($nodeNewPage);
        $newAcl = $this->aclProvider->createAcl($newIdentity);

        $aces = $originalAcl->getObjectAces();
        /* @var EntryInterface $ace */
        foreach ($aces as $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                $newAcl->insertObjectAce($securityIdentity, $ace->getMask());
            }
        }
        $this->aclProvider->updateAcl($newAcl);
    }

    private function cloneChildren(Node $originalNode, PageInterface $newPage, BaseUser $user, string $locale): void
    {
        $nodeChildren = $originalNode->getChildren();
        foreach ($nodeChildren as $originalNodeChild) {
            $originalNodeTranslations = $originalNodeChild->getNodeTranslation($locale, true);
            $originalRef = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);

            if (!$originalRef instanceof DuplicateSubPageInterface || !$originalRef->skipClone()) {
                $this->dispatch(new PreNodeDuplicateEvent($originalNodeChild), Events::PRE_DUPLICATE_WITH_CHILDREN);
                $newChildPage = $this->clonePage($originalNodeChild, $locale);
                $newChildPage->setParent($newPage);

                $newChildNode = $this->createNodeStructureForNewPage($originalNodeChild, $newChildPage, $user, $locale);
                $this->dispatch(new PostNodeDuplicateEvent($originalNodeChild, $newChildNode, $newChildPage), Events::POST_DUPLICATE_WITH_CHILDREN);
                $this->cloneChildren($originalNodeChild, $newChildPage, $user, $locale);
            }
        }
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($this->eventDispatcherInterface);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcherInterface->dispatch($eventName, $event);
    }
}
