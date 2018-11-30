<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * Get user(s) that have the specified role(s)
     *
     * @param string|array $role The role(s) for which you want to retrieve the users
     *
     * @return array
     */
    public function getUsersByRole($role)
    {
        if (is_array($role)) {
            $roles = $role;
        } else {
            $roles = array($role);
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from('KunstmaanAdminBundle:User', 'u')
            ->innerJoin('u.groups', 'g')
            ->innerJoin('g.roles', 'r')
            ->where('u.enabled=1')
            ->andWhere('r.role IN (:roles)')
            ->setParameter('roles', $roles);

        return $qb->getQuery()->getResult();
    }
}
