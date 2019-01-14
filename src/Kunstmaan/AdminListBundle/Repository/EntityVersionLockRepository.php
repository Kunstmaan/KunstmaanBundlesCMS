<?php

namespace Kunstmaan\AdminListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminListBundle\Entity\LockableEntity;
use FOS\UserBundle\Model\User;

/**
 * EntityVersionLockRepository
 */
class EntityVersionLockRepository extends EntityRepository
{
    /**
     * Check if there is a entity lock that's not passed the threshold.
     *
     * @param LockableEntity $entity
     * @param int            $threshold
     * @param User           $userToExclude
     *
     * @return EntityVersionLock[]
     */
    public function getLocksForLockableEntity(LockableEntity $entity, $threshold, User $userToExclude = null)
    {
        $qb = $this->createQueryBuilder('evl')
            ->select('evl')
            ->join('evl.lockableEntity', 'le')
            ->where('le.id = :e')
            ->andWhere('evl.createdAt > :date')
            ->setParameter('e', $entity->getId())
            ->setParameter('date', new \DateTime(sprintf('-%s seconds', $threshold)))
        ;

        if (!is_null($userToExclude)) {
            $qb->andWhere('evl.owner <> :owner')
                ->setParameter('owner', $userToExclude->getUsername())
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get locks that are passed the threshold.
     *
     * @param LockableEntity $entity
     * @param int            $threshold
     *
     * @return mixed
     */
    public function getExpiredLocks(LockableEntity $entity, $threshold)
    {
        $qb = $this->createQueryBuilder('evl')
            ->select('evl')
            ->join('evl.lockableEntity', 'le')
            ->where('le.id = :e')
            ->andWhere('evl.createdAt < :date')
            ->setParameter('e', $entity->getId())
            ->setParameter('date', new \DateTime(sprintf('-%s seconds', $threshold)));

        return $qb->getQuery()->getResult();
    }
}
