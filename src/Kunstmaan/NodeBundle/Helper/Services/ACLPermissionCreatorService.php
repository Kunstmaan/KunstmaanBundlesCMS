<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Class ACLPermissionCreatorService
 *
 * Service to add the correct permissions to new HasNodeInterface objects.
 *
 * @package Kunstmaan\NodeBundle\Helper\Services
 */
class ACLPermissionCreatorService
{
    /* @var MutableAclProviderInterface */
    protected $aclProvider;

    /* @var ObjectIdentityRetrievalStrategyInterface */
    protected $oidStrategy;

    /**
     * ACLPermissionCreatorService constructor.
     *
     * @param MutableAclProviderInterface|null $aclProvider
     * @param ObjectIdentityRetrievalStrategyInterface|null $oidStrategy
     */
    public function __construct(
        MutableAclProviderInterface $aclProvider = null,
        ObjectIdentityRetrievalStrategyInterface $oidStrategy = null
    ) {
        if ($aclProvider instanceof MutableAclProviderInterface) {
            $this->aclProvider = $aclProvider;
        }
        if ($oidStrategy instanceof ObjectIdentityRetrievalStrategyInterface) {
            $this->oidStrategy = $oidStrategy;
        }
    }

    /**
     * @param $oidStrategy
     */
    public function setObjectIdentityRetrievalStrategy($oidStrategy)
    {
        @trigger_error(
            'Setter injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->oidStrategy = $oidStrategy;
    }

    /**
     * @param $aclProvider
     */
    public function setAclProvider($aclProvider)
    {
        @trigger_error(
            'Setter injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->aclProvider = $aclProvider;
    }

    /**
     * Sets the Container. This is still here for backwards compatibility.
     * The ContainerAwareInterface has been removed so the container won't be injected automatically.
     * This function is just there for code that calls it manually.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     *
     * @api
     */
    public function setContainer(ContainerInterface $container)
    {
        @trigger_error(
            'Container injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );

        $this->setAclProvider($container->get('security.acl.provider'));
        $this->setObjectIdentityRetrievalStrategy($container->get('security.acl.object_identity_retrieval_strategy'));
    }

    /**
     * @param object $object
     *
     * Create ACL permissions for an object.
     */
    public function createPermission($object)
    {
        $objectIdentity = $this->oidStrategy->getObjectIdentity($object);
        try {
            $this->aclProvider->deleteAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            // Don't fail when the ACL didn't exist yet.
        }
        $acl = $this->aclProvider->createAcl($objectIdentity);

        $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

        $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $acl->insertObjectAce(
            $securityIdentity,
            MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_DELETE | MaskBuilder::MASK_PUBLISH | MaskBuilder::MASK_UNPUBLISH
        );

        $securityIdentity = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_IDDQD);
        $this->aclProvider->updateAcl($acl);
    }
}
