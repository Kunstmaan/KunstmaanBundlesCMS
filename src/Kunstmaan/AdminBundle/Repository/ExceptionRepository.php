<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ExceptionRepository extends EntityRepository
{
    public function findAllHigherThanDays(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('e')
            ->where('e.isMark = 1')
            ->andWhere('e.createdAt <= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
