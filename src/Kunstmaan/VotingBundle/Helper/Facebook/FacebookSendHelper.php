<?php

namespace Kunstmaan\VotingBundle\Helper\Facebook;
use Kunstmaan\VotingBundle\Helper\VotingHelper;

/**
 * Helper class for Facebook Sends
 */
class FacebookSendHelper extends VotingHelper
{

    /**
     * @param string $reference Reference to filter the Facebook Send by
     *
     * @return array Returns an array of Facebook Sends
     */
    public function byReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:Facebook\FacebookSend')
            ->findByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the Facebook Sends by
     *
     * @return mixed Returns the count of Facebook Sends
     */
    public function countByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:Facebook\FacebookSend')
            ->countByReference($reference);
    }

    /**
     * @param $reference The reference to filter the Facebook Sends by
     *
     * @return mixed Returns the sum of the values of the Facebook Sends
     */
    public function getValueByReference($reference)
    {
        return $this->em
            ->getRepository('KunstmaanVotingBundle:Facebook\FacebookSend')
            ->getValueByReference($reference);
    }
}