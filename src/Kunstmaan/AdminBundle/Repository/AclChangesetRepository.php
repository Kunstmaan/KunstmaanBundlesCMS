<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\AclChangeset;

/**
 * ACL changeset repository
 */
class AclChangesetRepository extends EntityRepository
{
    /**
     * Find a changeset with status RUNNING
     *
     * @return null|AclChangeset
     */
    public function findRunningChangeset()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('ac')
            ->where('ac.status = :status')
            ->addOrderBy('ac.id', 'ASC')
            ->setMaxResults(1)
            ->setParameter('status', AclChangeset::STATUS_RUNNING);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Fetch the oldest acl changeset for state NEW
     *
     * @return null|AclChangeset
     */
    public function findNewChangeset()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('ac')
            ->where('ac.status = :status')
            ->addOrderBy('ac.id', 'ASC')
            ->setMaxResults(1)
            ->setParameter('status', AclChangeset::STATUS_NEW);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Check if there are pending changesets
     *
     * @return bool
     */
    public function hasPendingChangesets()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('count(ac)')
            ->where('ac.status = :status')
            ->setParameter('status', AclChangeset::STATUS_NEW);

        return $qb->getQuery()->getSingleScalarResult() != 0;
    }
}
