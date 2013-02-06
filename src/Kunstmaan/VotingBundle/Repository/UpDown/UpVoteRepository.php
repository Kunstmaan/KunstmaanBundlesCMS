<?php

namespace Kunstmaan\VotingBundle\Repository\Facebook;

use Doctrine\ORM\EntityRepository;

/**
 * Repository class for Up votes
 */
class UpVoteRepository extends EntityRepository
{

    /**
     * @param string $reference The reference to filter the Up votes by
     *
     * @return array Returns an array of Up votes
     */
    public function findByReference($reference)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param string $reference The reference to filter the Up votes by
     *
     * @return mixed Returns the count of Up votes
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
     * @param $reference The reference to filter the Up votes by
     *
     * @return mixed Returns the sum of the values of the Up votes
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