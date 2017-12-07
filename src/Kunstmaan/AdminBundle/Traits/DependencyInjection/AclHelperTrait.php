<?php

namespace  Kunstmaan\AdminBundle\Traits\DependencyInjection;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

/**
 * Trait AclHelperTrait
 */
trait AclHelperTrait
{
    /**
     * @var AclHelper $aclHelper
     */
    protected $aclHelper;

    /**
     * @return AclHelper
     */
    public function getAclHelper()
    {
        if (null !== $this->container && null === $this->aclHelper) {
            $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
        }

        return $this->aclHelper;
    }

    /**
     * @required
     * @param AclHelper $aclHelper
     */
    public function setAclHelper(AclHelper $aclHelper = null)
    {
        $this->aclHelper = $aclHelper;
    }

}
