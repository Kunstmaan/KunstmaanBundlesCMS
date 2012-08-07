<?php

namespace Kunstmaan\AdminBundle\Permission;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

use Kunstmaan\AdminBundle\Entity\User;

class Manager
{

    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getCurrentUser($user) 
    {
        if (!$user instanceof User) {
            $user = $this->em->getRepository('KunstmaanAdminBundle:User')->findOneBy(array('username' => 'guest'));
        }

        return $user;
    }

}