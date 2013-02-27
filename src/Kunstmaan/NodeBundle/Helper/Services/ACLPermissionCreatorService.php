<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Symfony\Component\DependencyInjection\ContainerAwareInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission,
    Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity,
    Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder,
    Symfony\Component\Security\Acl\Exception\AclNotFoundException;

/**
 * Service to add the correct permissions to new HasNodeInterface objects.
 */
class ACLPermissionCreationService Implements ContainerAwareInterface
{

    /**
     * Create ACL permissions for an object.
     */
    public function createPermission($object) {
        $container = $this->container;

        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $oidStrategy */
        $oidStrategy = $container->get('security.acl.object_identity_retrieval_strategy');

        $objectIdentity = $oidStrategy->getObjectIdentity($object);
        try {
            $aclProvider->deleteAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            // Do nothing
        }
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityIdentity = new RoleSecurityIdentity('ROLE_GUEST');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

        $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $acl->insertObjectAce(
            $securityIdentity,
            MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_PUBLISH | MaskBuilder::MASK_UNPUBLISH
        );

        $securityIdentity = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_IDDQD);
        $aclProvider->updateAcl($acl);
    }


    protected $container;
    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
