<?php

namespace Kunstmaan\VotingBundle\Helper\LinkedIn;

use Kunstmaan\VotingBundle\Helper\VotingHelper;

/**
 * Helper class for LinkedIn Shares
 */
class LinkedInShareHelper extends VotingHelper
{

    /**
     * @param string $reference Reference to filter the LinkedIn Share by
     *
     * @return array Returns an array of LinkedIn Shares
     */
    public function byReference($reference)
    {
        return $this->em->getRepository('KunstmaanVotingBundle:LinkedIn\LinkedInShare')->findByReference($reference);
    }

    /**
     * @param string $reference The reference to filter the LinkedIn Shares by
     *
     * @return mixed Returns the count of LinkedIn Shares
     */
    public function countByReference($reference)
    {
        return $this->em->getRepository('KunstmaanVotingBundle:LinkedIn\LinkedInShare')->countByReference($reference);
    }

    /**
     * @param $reference The reference to filter the LinkedIn Shares by
     *
     * @return mixed Returns the sum of the values of the LinkedIn Shares
     */
    public function getValueByReference($reference)
    {
        return $this->em->getRepository('KunstmaanVotingBundle:LinkedIn\LinkedInShare')->getValueByReference($reference);
    }
}