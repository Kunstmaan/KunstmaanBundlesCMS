<?php

namespace Kunstmaan\AdminBundle\Repository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function getUser($user_id, EntityManager $em)
    {
        $user = $em->getRepository('KunstmaanAdminBundle:User')->find($user_id);
        if (!$user) {
            throw new NotFoundHttpException('The id given for the user is not valid.');
        }

        return $user;
    }

    /**
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
            ->andWhere('u.locked=0')
            ->andWhere('u.expired=0')
            ->andWhere('r.role IN (:roles)')
            ->setParameter('roles', $roles);

        return $qb->getQuery()->getResult();
    }

}
