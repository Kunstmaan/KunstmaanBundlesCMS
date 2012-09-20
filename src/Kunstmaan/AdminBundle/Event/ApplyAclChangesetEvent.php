<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Kunstmaan\AdminBundle\Entity\AclChangeset;

/**
 * ApplyAclChangesetEvent
 */
class ApplyAclChangesetEvent extends Event
{
    /**
     * @var AclChangeset $aclChangeset
     */
    protected $aclChangeset;

    /**
     * @param AclChangeset $aclChangeset
     */
    public function __construct(AclChangeset $aclChangeset)
    {
        $this->aclChangeset = $aclChangeset;
    }

    /**
     * @return AclChangeset
     */
    public function getAclChangeset()
    {
        return $this->aclChangeset;
    }
}
