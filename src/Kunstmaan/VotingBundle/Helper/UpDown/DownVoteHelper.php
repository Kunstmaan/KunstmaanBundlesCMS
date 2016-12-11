<?php

namespace Kunstmaan\VotingBundle\Helper\UpDown;

use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;

/**
 * Helper class for Down votes
 */
class DownVoteHelper extends AbstractVotingHelper
{
    /**
     * @var string
     */
    protected $repository = 'KunstmaanVotingBundle:UpDown\DownVote';
}
