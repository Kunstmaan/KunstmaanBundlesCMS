<?php

namespace Kunstmaan\CookieBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\CookieBundle\Entity\CookieConfig;

/**
 * Class CookieRepository
 */
class CookieConfigRepository extends EntityRepository
{
    /**
     * @return CookieConfig
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLatestConfig()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
