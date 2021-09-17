<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ExceptionRepository extends EntityRepository
{
    public function findAllHigherThanDays(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('e')
            ->where('e.isResolved = :isResolved')
            ->andWhere('e.createdAt <= :date')
            ->setParameter('date', $date)
            ->setParameter('isResolved', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function markAllAsResolved()
    {
        return $this->createQueryBuilder('e')
            ->update()
            ->set('e.isResolved', ':isResolvedTrue')
            ->where('e.isResolved = :isResolvedFalse')
            ->setParameter('isResolvedTrue', true)
            ->setParameter('isResolvedFalse', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findExceptionStatistics()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id) as cp_all, SUM(e.events) as cp_sum')
            ->where('e.isResolved = :isResolved')
            ->setParameter('isResolved', false)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
