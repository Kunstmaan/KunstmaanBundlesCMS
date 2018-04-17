<?php

namespace Kunstmaan\VotingBundle\Helper\Facebook;

use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;

/**
 * Helper class for Facebook Likes
 */
class FacebookLikeHelper extends AbstractVotingHelper
{
    /**
     * @var string
     */
    protected $repository = 'KunstmaanVotingBundle:Facebook\FacebookLike';
}
