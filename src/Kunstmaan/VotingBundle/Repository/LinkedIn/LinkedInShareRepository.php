<?php

namespace Kunstmaan\VotingBundle\Repository\LinkedIn;

use Doctrine\ORM\EntityRepository;

/**
 * Repository class for LinkedIn Shares
 */
class LinkedInShareRepository extends EntityRepository
{

    /**
     * @param string $reference The reference to filter the LinkedIn Shares by
     *
     * @return array Returns an array of LinkedIn Shares
     */
    public function findByReference($reference)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param string $reference The reference to filter the LinkedIn Shares by
     *
     * @return mixed Returns the count of LinkedIn Shares
     */
    public function countByReference($reference)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $reference The reference to filter the LinkedIn Shares by
     *
     * @return mixed Returns the sum of the values of the LinkedIn Shares
     */
    public function getValueByReference($reference)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('SUM(e.value)')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb->getQuery()->getSingleScalarResult();
    }
}