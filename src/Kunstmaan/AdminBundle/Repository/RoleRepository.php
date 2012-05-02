<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    public function getRole(role_id, EntityManager $em) {
        $role = $em->getRepository('KunstmaanAdminBundle:Role')->find($role_id);
        if (!$role) {
            throw new NotFoundHttpException('The id given for the role is not valid.');
        }
        return $role;
    }
}