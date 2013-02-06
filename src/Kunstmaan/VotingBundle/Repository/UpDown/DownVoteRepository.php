<?php

namespace Kunstmaan\VotingBundle\Repository\Facebook;

use Doctrine\ORM\EntityRepository;

/**
 * Repository class for Down votes
 */
class DownVoteRepository extends EntityRepository
{

    /**
     * @param string $reference The reference to filter the Down votes by
     *
     * @return array Returns an array of Down votes
     */
    public function findByReference($reference)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param string $reference The reference to filter the Down votes by
     *
     * @return mixed Returns the count of Down votes
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
     * @param $reference The reference to filter the Down votes by
     *
     * @return mixed Returns the sum of the values of the Down votes
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