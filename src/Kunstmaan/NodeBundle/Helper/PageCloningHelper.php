<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\IgnoreDuplicateAsChildInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

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

    public function __construct(EntityManagerInterface $em, CloneHelper $cloneHelper, AclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $identityRetrivalStrategy)
    {
        $this->em = $em;
        $this->cloneHelper = $cloneHelper;
        $this->aclProvider = $aclProvider;
        $this->identityRetrievalStrategy = $identityRetrivalStrategy;
    }

    public function createNodeStructureForNewPage(Node $originalNode, HasNodeInterface $newPage, BaseUser $user, string $locale)
    {
        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor(
            $newPage,
            $locale,
            $user
        );

        if ($newPage->isStructureNode()) {
            $nodeTranslation = $nodeNewPage->getNodeTranslation($locale, true);
            $nodeTranslation->setSlug('');
            $this->em->persist($nodeTranslation);
        }
        $this->em->flush();

        $this->updateAcl($originalNode, $nodeNewPage);

        return $nodeNewPage;
    }

    public function clonePage(Node $originalNode, $locale, $title = null)
    {
        $originalNodeTranslations = $originalNode->getNodeTranslation($locale, true);
        $originalRef = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);

        $newPage = $this->cloneHelper
            ->deepCloneAndSave($originalRef);

        //set the title
        if (is_string($title) && !empty($title)) {
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

    public function cloneChildren(Node $originalNode, PageInterface $newPage, BaseUser $user, string $locale)
    {
        $nodeChildren = $originalNode->getChildren();
        /** @var Node $originalNodeChild */
        foreach ($nodeChildren as $originalNodeChild) {
            $originalNodeTranslations = $originalNodeChild->getNodeTranslation($locale, true);
            $originalRef = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);

            if (!$originalRef instanceof IgnoreDuplicateAsChildInterface) {
                $newChildPage = $this->clonePage($originalNodeChild, $locale);
                $newChildPage->setParent($newPage);
                $this->createNodeStructureForNewPage($originalNodeChild, $newChildPage, $user, $locale);
                $this->cloneChildren($originalNodeChild, $newChildPage, $user, $locale);
            }
        }
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
}
