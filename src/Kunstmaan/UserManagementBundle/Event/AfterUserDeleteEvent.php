<?php

namespace Kunstmaan\UserManagementBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;

final class AfterUserDeleteEvent extends BcEvent
{
    /** @var string */
    private $deletedUser;

    /** @var string */
    private $deletedBy;

    public function __construct(string $deletedUser, string $deletedBy)
    {
        $this->deletedUser = $deletedUser;
        $this->deletedBy = $deletedBy;
    }

    public function getDeletedUser(): string
    {
        return $this->deletedUser;
    }

    public function getDeletedBy(): string
    {
        return $this->deletedBy;
    }
}
