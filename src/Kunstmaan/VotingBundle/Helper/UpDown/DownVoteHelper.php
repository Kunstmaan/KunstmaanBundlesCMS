<?php

namespace Kunstmaan\VotingBundle\Helper\UpDown;

use Kunstmaan\VotingBundle\Helper\VotingHelper;

/**
 * Helper class for Down votes
 */
class DownVoteHelper extends VotingHelper
{

    /**
     * @param string $reference Reference to filter the Down votes by
     *
     * @return array Returns an array of Down votes
     */
    public function byReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:UpDown\DownVote')
            ->findByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the Down votes by
     *
     * @return mixed Returns the count of Down votes
     */
    public function countByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:UpDown\DownVote')
            ->countByReference($reference);
    }

    /**
     * @param $reference The reference to filter the Down votes by
     *
     * @return mixed Returns the sum of the values of the Down votes
     */
    public function getValueByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:UpDown\DownVote')
            ->getValueByReference($reference);
    }
}
