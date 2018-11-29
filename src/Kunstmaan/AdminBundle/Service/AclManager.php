<?php

namespace Kunstmaan\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Entity\AclChangeset;

/**
 * Class AclManager
 */
class AclManager
{
    /** @var MutableAclProviderInterface */
    private $aclProvider;

    /** @var ObjectIdentityRetrievalStrategyInterface */
    private $objectIdentityRetrievalStrategy;

    /** @var EntityManagerInterface */
    private $em;

    /** @var PermissionAdmin */
    private $permissionAdmin;

    public function __construct(MutableAclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $objectIdentityRetrievalStrategy, EntityManagerInterface $em, PermissionAdmin $permissionAdmin)
    {
        $this->aclProvider = $aclProvider;
        $this->objectIdentityRetrievalStrategy = $objectIdentityRetrievalStrategy;
        $this->em = $em;
        $this->permissionAdmin = $permissionAdmin;
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
     * @param array  $nodes
     * @param string $role
     * @param int    $mask
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

    public function applyAclChangesets()
    {
        /* @var AclChangesetRepository $aclRepo */
        $aclRepo = $this->em->getRepository('KunstmaanAdminBundle:AclChangeset');
        do {
            /* @var AclChangeset $changeset */
            $changeset = $aclRepo->findNewChangeset();
            if (is_null($changeset)) {
                break;
            }

            $this->applyAclChangeSet($changeset);

            $hasPending = $aclRepo->hasPendingChangesets();
        } while ($hasPending);
    }

    /**
     * @param AclChangeset $aclChangeset
     */
    public function applyAclChangeSet(AclChangeset $aclChangeset)
    {
        $aclChangeset->setPid(getmypid());
        $aclChangeset->setStatus(AclChangeset::STATUS_RUNNING);
        $this->em->persist($aclChangeset);
        $this->em->flush($aclChangeset);

        $entity = $this->em->getRepository($aclChangeset->getRefEntityName())->find($aclChangeset->getRefId());
        $this->permissionAdmin->applyAclChangeset($entity, $aclChangeset->getChangeset());

        $aclChangeset->setStatus(AclChangeset::STATUS_FINISHED);
        $this->em->persist($aclChangeset);
        $this->em->flush($aclChangeset);
    }
}
