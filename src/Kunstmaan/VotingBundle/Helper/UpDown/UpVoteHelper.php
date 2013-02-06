<?php

namespace Kunstmaan\VotingBundle\Helper\UpDown;

use Kunstmaan\VotingBundle\Helper\VotingHelper;

/**
 * Helper class for Up votes
 */
class UpVoteHelper extends VotingHelper
{

    /**
     * @param string $reference Reference to filter the Up votes by
     *
     * @return array Returns an array of Up votes
     */
    public function byReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:UpDown\UpVote')
            ->findByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the Up votes by
     *
     * @return mixed Returns the count of Up votes
     */
    public function countByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:UpDown\UpVote')
            ->countByReference($reference);
    }

    /**
     * @param $reference The reference to filter the Up votes by
     *
     * @return mixed Returns the sum of the values of the Up votes
     */
    public function getValueByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:UpDown\UpVote')
            ->getValueByReference($reference);
    }
}