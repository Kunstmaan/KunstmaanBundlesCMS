<?php

namespace Kunstmaan\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Kunstmaan\AdminBundle\Entity\AclChangeset;

class ApplyAclChangesetEvent extends Event
{
    /* @var AclChangeset $aclChangeset */
    protected $aclChangeset;

    public function __construct(AclChangeset $aclChangeset)
    {
        $this->aclChangeset = $aclChangeset;
    }

    public function getAclChangeset()
    {
        return $this->aclChangeset;
    }
}
