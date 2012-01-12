<?php

namespace Kunstmaan\AdminBundle\Permission;

use Kunstmaan\AdminBundle\Entity\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

class Manager
{

    public function getCurrentUser($user, $em) {
        if(! $user instanceof User) {
            $user = $em->getRepository('KunstmaanAdminBundle:User')->findOneBy(array('username' => 'guest'));
        }

        return $user;
    }

    public function hasPermision($entity, $user, $permission, EntityManager $em)
    {
        $user = $this->getCurrentUser($user, $em);
        try {
            $result = $em->getRepository('KunstmaanAdminBundle:Permission')->getPermission(
                get_class($entity),
                $entity->getId(),
                $user->getGroupIds(),
                $permission,
                $em
            );
            //catch NoResultException, if we have one of those we don't have the correct permission
        } catch(NoResultException $e) {
            return false;
        }

        //permission found, so return true
        return true;
    }
}