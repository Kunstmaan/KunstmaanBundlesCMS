<?php

namespace Kunstmaan\VotingBundle\Helper\UpDown;

use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;

/**
 * Helper class for Up votes
 */
class UpVoteHelper extends AbstractVotingHelper
{
    /**
     * @var string
     */
    protected $repository = 'KunstmaanVotingBundle:UpDown\UpVote';
}
