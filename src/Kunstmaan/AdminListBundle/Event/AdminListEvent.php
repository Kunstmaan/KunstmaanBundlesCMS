<?php

namespace Kunstmaan\AdminListBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AdminListEvent extends Event {

    private $adminList;

    function __construct($adminList)
    {
        $this->adminList = $adminList;
    }

    /**
     * @return mixed
     */
    public function getAdminList()
    {
        return $this->adminList;
    }

    /**
     * @param mixed $adminList
     * @return $this
     */
    public function setAdminList($adminList)
    {
        $this->adminList = $adminList;
        return $this;
    }
} 