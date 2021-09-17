<?php

namespace Kunstmaan\AdminListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\User;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminListBundle\Entity\LockableEntity;

/**
 * EntityVersionLockRepository
 */
class EntityVersionLockRepository extends EntityRepository
{
    /**
     * Check if there is a entity lock that's not passed the threshold.
     *
     * @param int  $threshold
     * @param User $userToExclude
     *
     * @return LockableEntity[]
     */
    public function getLocksForLockableEntity(LockableEntity $entity, $threshold, /*\Kunstmaan\AdminBundle\Entity\UserInterface*/ $userToExclude = null)
    {
        // NEXT_MAJOR: remove type check and enable parameter typehint
        if (!$userToExclude instanceof User && !$userToExclude instanceof UserInterface) {
            throw new \InvalidArgumentException(sprintf('The "$userToExclude" argument must be of type "%s" or implement the "%s" interface', User::class, UserInterface::class));
        }

        $qb = $this->createQueryBuilder('evl')
            ->select('evl')
            ->join('evl.lockableEntity', 'le')
            ->where('le.id = :e')
            ->andWhere('evl.createdAt > :date')
            ->setParameter('e', $entity->getId())
            ->setParameter('date', new \DateTime(sprintf('-%s seconds', $threshold)))
        ;

        if (!\is_null($userToExclude)) {
            $qb->andWhere('evl.owner <> :owner')
                ->setParameter('owner', $userToExclude->getUsername())
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get locks that are passed the threshold.
     *
     * @param int $threshold
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
