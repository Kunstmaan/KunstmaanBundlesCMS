<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Service to add the correct permissions to new HasNodeInterface objects.
 */
class ACLPermissionCreatorService
{
    /* @var MutableAclProviderInterface $aclProvider */
    protected $aclProvider;

    public function setAclProvider($aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    /* @var ObjectIdentityRetrievalStrategyInterface $oidStrategy */
    protected $oidStrategy;

    public function setObjectIdentityRetrievalStrategy($oidStrategy)
    {
        $this->oidStrategy = $oidStrategy;
    }

    /**
     * Sets the Container. This is still here for backwards compatibility.
     * The ContainerAwareInterface has been removed so the container won't be injected automatically.
     * This function is just there for code that calls it manually.
     *
     * @param ContainerInterface $container a ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->setAclProvider($container->get('security.acl.provider'));
        $this->setObjectIdentityRetrievalStrategy($container->get('security.acl.object_identity_retrieval_strategy'));
    }

    /**
     * @param object $object
     *
     * Create ACL permissions for an object
     */
    public function createPermission($object)
    {
        $aclProvider = $this->aclProvider;

        $oidStrategy = $this->oidStrategy;

        $objectIdentity = $oidStrategy->getObjectIdentity($object);

        try {
            $aclProvider->deleteAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            // Don't fail when the ACL didn't exist yet.
        }
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

        $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $acl->insertObjectAce(
            $securityIdentity,
            MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_DELETE | MaskBuilder::MASK_PUBLISH | MaskBuilder::MASK_UNPUBLISH
        );

        $securityIdentity = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_IDDQD);
        $aclProvider->updateAcl($acl);
    }
}
