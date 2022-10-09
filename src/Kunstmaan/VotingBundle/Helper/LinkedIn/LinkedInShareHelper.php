<?php

namespace Kunstmaan\VotingBundle\Helper\LinkedIn;

use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;
use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;

/**
 * Helper class for LinkedIn Shares
 */
class LinkedInShareHelper extends AbstractVotingHelper
{
    /**
     * @var string
     */
    protected $repository = LinkedInShare::class;
}
