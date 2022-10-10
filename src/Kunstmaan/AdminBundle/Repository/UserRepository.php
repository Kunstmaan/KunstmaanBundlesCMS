<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository implements PasswordUpgraderInterface
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
        if (\is_array($role)) {
            $roles = $role;
        } else {
            $roles = [$role];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.groups', 'g')
            ->innerJoin('g.roles', 'r')
            ->where('u.enabled= :enabled')
            ->andWhere('r.role IN (:roles)')
            ->setParameter('roles', $roles)
            ->setParameter('enabled', true);

        return $qb->getQuery()->getResult();
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        $user->setPassword($newEncodedPassword);

        $this->getEntityManager()->flush();
    }
}
