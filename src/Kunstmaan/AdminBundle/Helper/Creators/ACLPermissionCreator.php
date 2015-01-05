<?php

namespace Kunstmaan\AdminBundle\Helper\Creators;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Service to add the correct permissions to objects.
 */
class ACLPermissionCreator
{
    /**
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    /**
     * @var ObjectIdentityRetrievalStrategyInterface
     */
    private $objectIdentityRetrievalStrategy;

    /**
     * @param MutableAclProviderInterface $aclProvider
     * @param ObjectIdentityRetrievalStrategyInterface $objectIdentityRetrievalStrategy
     */
    public function __construct(MutableAclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $objectIdentityRetrievalStrategy)
    {
        $this->aclProvider = $aclProvider;
        $this->objectIdentityRetrievalStrategy = $objectIdentityRetrievalStrategy;
    }

    /**
     * @param mixed $object
     * @param mixed $example
     * @param bool $force
     */
    public function initByExample($object, $example, $force = false)
    {
        $aclProvider = $this->aclProvider;
        $strategy = $this->objectIdentityRetrievalStrategy;

        $exampleIdentity = $strategy->getObjectIdentity($example);
        $exampleAcl = $aclProvider->findAcl($exampleIdentity);

        $aces = array();
        /* @var EntryInterface $ace */
        foreach ($exampleAcl->getObjectAces() as $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                $aces[] = array(
                    'identity' => $securityIdentity,
                    'mask' => $ace->getMask()
                );
            }
        }
        $this->init($object, $aces, $force);
    }

    /**
     * @param mixed $object
     * @param array $aces
     * @param bool $force
     */
    private function init($object, $aces, $force = false)
    {
        $aclProvider = $this->aclProvider;
        $strategy = $this->objectIdentityRetrievalStrategy;

        $objectIdentity = $strategy->getObjectIdentity($object);
        if ($force || $aclProvider->findAcl($objectIdentity) === null) {
            try {
                $aclProvider->deleteAcl($objectIdentity);
            } catch (AclNotFoundException $e) {
                // Do nothing
            }

            $acl = $aclProvider->createAcl($objectIdentity);

            foreach ($aces as $ace) {
                $acl->insertObjectAce($ace['identity'], $ace['mask']);
            }

            $aclProvider->updateAcl($acl);
        }
    }

    /**
     * @param mixed $object
     * @param array $map
     *        with as key the name of the role you want to set the permissions for
     *        and as value the mask you want to use
     *        for example array('ROLE_GUEST' => MaskBuilder::MASK_EDIT | MaskBuilder::MASK_PUBLISH)
     * @param bool $force
     */
    public function initByMap($object, $map, $force = false)
    {
        $aces = array();
        foreach ($map as $key => $value) {
            $aces[] = array(
                'identity' => new RoleSecurityIdentity($key),
                'mask' => $value
            );
        }

        $this->init($object, $aces, $force);
    }
}
