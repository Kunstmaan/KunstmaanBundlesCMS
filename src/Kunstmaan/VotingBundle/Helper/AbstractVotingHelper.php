<?php

namespace Kunstmaan\VotingBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Abstract Helper class for Voting
 */
abstract class AbstractVotingHelper
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $reference Reference to filter the votes by
     *
     * @return array Returns an array of votes
     */
    public function byReference($reference)
    {
        return $this->em->getRepository($this->repository)
            ->findByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the votes by
     *
     * @return mixed Returns the count
     */
    public function countByReference($reference)
    {
        return $this->em->getRepository($this->repository)
            ->countByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the votes by
     *
     * @return mixed Returns the sum of the values
     */
    public function getValueByReference($reference)
    {
        return $this->em->getRepository($this->repository)
            ->getValueByReference($reference);
    }
}
