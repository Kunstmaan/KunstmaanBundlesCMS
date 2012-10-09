<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Kunstmaan\AdminBundle\Entity\AclChangeset;

/**
 * ApplyAclChangesetEvent will be used when the {@link Events::APPLY_ACL_CHANGESET} event is triggered in the
 * {@link PermissionAdmin::bindRequest()} function to pass state information to the listeners of this event
 */
class ApplyAclChangesetEvent extends Event
{
    /**
     * The changeset that will be applied
     *
     * @var AclChangeset $aclChangeset
     */
    protected $aclChangeset;

    /**
     * Constructor
     *
     * @param AclChangeset $aclChangeset
     */
    public function __construct(AclChangeset $aclChangeset)
    {
        $this->aclChangeset = $aclChangeset;
    }

    /**
     * Get ACL changeset
     *
     * @return AclChangeset
     */
    public function getAclChangeset()
    {
        return $this->aclChangeset;
    }
}
