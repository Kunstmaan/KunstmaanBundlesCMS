<?php
namespace Kunstmaan\VotingBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AbstractVote Repository class
 */
class AbstractVoteRepository extends EntityRepository
{

    /**
     * @param string $reference The reference to filter the votes by
     *
     * @return array Returns an array of votes
     */
    public function findByReference($reference)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param string $reference The reference to filter the votes by
     *
     * @return mixed Returns the count of votes
     */
    public function countByReference($reference)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

     /**
     * @param string $reference The reference to filter the votes by
     * @param string $ip        The ip to filter the votes by
     *
     * @return mixed Returns the count of votes
     */
    public function countByReferenceAndByIp($reference, $ip)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.reference = :reference')
            ->andWhere('e.ip = :ip')
            ->setParameter('ip', $ip)
            ->setParameter('reference', $reference);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $reference The reference to filter the votes by
     *
     * @return mixed Returns the sum of the values of the votes
     */
    public function getValueByReference($reference)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->select('SUM(e.value)')
            ->where('e.reference = :reference')
            ->setParameter('reference', $reference);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }
}
