<?php

namespace Kunstmaan\VotingBundle\Helper\Facebook;

use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;
use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;

/**
 * Helper class for Facebook Sends
 */
class FacebookSendHelper extends AbstractVotingHelper
{
    /**
     * @var string
     */
    protected $repository = FacebookSend::class;
}
