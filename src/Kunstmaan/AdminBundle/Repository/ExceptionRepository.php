<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ExceptionRepository extends EntityRepository
{
    public function findAllHigherThanDays(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('e')
            ->where('e.isResolved = :isResolvedTrue')
            ->andWhere('e.createdAt <= :date')
            ->setParameter('date', $date)
            ->setParameter('isResolvedTrue', true)
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
        if ($this->_em->getConnection()->getDatabasePlatform()->getName()=='postgresql'){
            return $this->createQueryBuilder('e')
                ->update()
                ->set('e.isResolved',':isResolvedTrue')
                ->where('e.isResolved = :isResolvedFalse')
                ->setParameter('isResolvedTrue',true,\PDO::PARAM_BOOL)
                ->setParameter('isResolvedFalse',false,\PDO::PARAM_BOOL)
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            return $this->createQueryBuilder('e')
                ->update()
                ->set('e.isResolved',1)
                ->where('e.isResolved = :isResolvedFalse')
                ->setParameter('isResolvedFalse',false)
                ->getQuery()
                ->getSingleScalarResult();
        }

    }

    public function findExceptionStatistics()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id) as cp_all, SUM(e.events) as cp_sum')
            ->where('e.isResolved = :isResolvedFalse')
            ->setParameter('isResolvedFalse',false)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
