<?php

namespace Kunstmaan\AdminBundle\Service;

use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Class AclManager
 * @package Kunstmaan\AdminBundle\Security
 */
class AclManager
{
    /** @var MutableAclProviderInterface */
    private $aclProvider;

    /** @var ObjectIdentityRetrievalStrategyInterface */
    private $strategy;

    public function __construct(MutableAclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $strategy)
    {
        $this->aclProvider = $aclProvider;
        $this->strategy = $strategy;
    }

    /**
     * @param $originalNode
     * @param $nodeNewPage
     */
    public function updateNodeAcl(Node $originalNode, Node $nodeNewPage)
    {
        $originalIdentity = $this->strategy->getObjectIdentity($originalNode);
        $originalAcl = $this->aclProvider->findAcl($originalIdentity);

        $newIdentity = $this->strategy->getObjectIdentity($nodeNewPage);
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
