<?php

namespace Kunstmaan\AdminListBundle\Tests\Model;

use Kunstmaan\AdminListBundle\Entity\LockableEntityInterface;

class TestLockableEntityInterfaceImplementation implements LockableEntityInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->setId($id);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
