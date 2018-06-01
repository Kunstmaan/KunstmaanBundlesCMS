<?php

namespace Kunstmaan\AdminBundle\Service;

use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

/**
 * Class AclManager
 * @package Kunstmaan\AdminBundle\Security
 */
class AclManager
{
    /** @var MutableAclProviderInterface */
    private $aclProvider;

    /** @var ObjectIdentityRetrievalStrategyInterface */
    private $objectIdentityRetrievalStrategy;

    public function __construct(MutableAclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $objectIdentityRetrievalStrategy)
    {
        $this->aclProvider = $aclProvider;
        $this->objectIdentityRetrievalStrategy = $objectIdentityRetrievalStrategy;
    }

    /**
     * @param $originalNode
     * @param $nodeNewPage
     */
    public function updateNodeAcl(Node $originalNode, Node $nodeNewPage)
    {
        $originalIdentity = $this->objectIdentityRetrievalStrategy->getObjectIdentity($originalNode);
        $originalAcl = $this->aclProvider->findAcl($originalIdentity);

        $newIdentity = $this->objectIdentityRetrievalStrategy->getObjectIdentity($nodeNewPage);
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

    /**
     * @param array     $nodes
     * @param string    $role
     * @param int       $mask
     */
    public function updateNodesAclToRole(array $nodes, $role, $mask)
    {
        foreach ($nodes as $node) {
            $objectIdentity = $this->objectIdentityRetrievalStrategy->getObjectIdentity($node);

            /** @var Acl $acl */
            $acl = $this->aclProvider->findAcl($objectIdentity);
            $securityIdentity = new RoleSecurityIdentity($role);

            /** @var Entry $ace */
            foreach ($acl->getObjectAces() as $index => $ace) {
                if (!$ace->getSecurityIdentity()->equals($securityIdentity)) {
                    continue;
                }
                $acl->updateObjectAce($index, $mask);
                break;
            }
            $this->aclProvider->updateAcl($acl);
        }
    }
}
