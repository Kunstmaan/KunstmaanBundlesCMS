<?php

namespace Kunstmaan\AdminBundle\Entity;

use FOS\UserBundle\Model\GroupableInterface as FosGroupableInterface;
use FOS\UserBundle\Model\UserInterface as FosUserInterface;

interface UserInterface extends FosUserInterface, FosGroupableInterface
{
    //NEXT_MAJOR add these constants to this interface (after fos user interface is removed)
//    const ROLE_DEFAULT = 'ROLE_USER';
//    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
