<?php

namespace Kunstmaan\AdminBundle\Entity;

use FOS\UserBundle\Model\GroupableInterface as FosGroupableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, FosGroupableInterface
{
    //NEXT_MAJOR add these constants to this interface (after fos user interface is removed)
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
