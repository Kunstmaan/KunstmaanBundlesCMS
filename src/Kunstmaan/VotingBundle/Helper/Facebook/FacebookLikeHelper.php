<?php

namespace Kunstmaan\VotingBundle\Helper\Facebook;
use Kunstmaan\VotingBundle\Helper\VotingHelper;

/**
 * Helper class for Facebook Likes
 */
class FacebookLikeHelper extends VotingHelper
{

    /**
     * @param string $reference Reference to filter the Facebook Like by
     *
     * @return array Returns an array of Facebook Likes
     */
    public function byReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:Facebook\FacebookLike')
            ->findByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the Facebook Likes by
     *
     * @return mixed Returns the count of Facebook Likes
     */
    public function countByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:Facebook\FacebookLike')
            ->countByReference($reference);
    }

    /**
     * @param $reference The reference to filter the Facebook Likes by
     *
     * @return mixed Returns the sum of the values of the Facebook Likes
     */
    public function getValueByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:Facebook\FacebookLike')
            ->getValueByReference($reference);
    }
}
