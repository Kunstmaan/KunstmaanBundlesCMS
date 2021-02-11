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

    /* @var ObjectIdentityRetrievalStrategyInterface $oidStrategy */
    protected $oidStrategy;

    public function __construct(MutableAclProviderInterface $aclProvider = null, ObjectIdentityRetrievalStrategyInterface $oidStrategy = null)
    {
        if (null === $aclProvider) {
            @trigger_error(sprintf('Not injecting the required dependencies in the constructor of "%s" is deprecated since KunstmaanNodeBundle 5.7 and will be required in KunstmaanNodeBundle 6.0.', __CLASS__), E_USER_DEPRECATED);
        }

        $this->aclProvider = $aclProvider;
        $this->oidStrategy = $oidStrategy;
    }

    /**
     * @deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Inject the required dependencies in the constructor instead.
     */
    public function setAclProvider($aclProvider)
    {
        @trigger_error(sprintf('Using the "%s" method is deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Inject the required dependencies in the constructor instead.', __METHOD__), E_USER_DEPRECATED);

        $this->aclProvider = $aclProvider;
    }

    /**
     * @deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Inject the required dependencies in the constructor instead.
     */
    public function setObjectIdentityRetrievalStrategy($oidStrategy)
    {
        @trigger_error(sprintf('Using the "%s" method is deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Inject the required dependencies in the constructor instead.', __METHOD__), E_USER_DEPRECATED);

        $this->oidStrategy = $oidStrategy;
    }

    /**
     * Sets the Container. This is still here for backwards compatibility.
     * The ContainerAwareInterface has been removed so the container won't be injected automatically.
     * This function is just there for code that calls it manually.
     *
     * @param ContainerInterface $container a ContainerInterface instance
     *
     * @deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Inject the required dependencies in the constructor instead.
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        @trigger_error(sprintf('Using the "%s" method is deprecated since KunstmaanNodeBundle 5.7 and will be removed in KunstmaanNodeBundle 6.0. Inject the required dependencies in the constructor instead.', __METHOD__), E_USER_DEPRECATED);

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
