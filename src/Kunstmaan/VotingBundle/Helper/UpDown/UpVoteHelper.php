<?php

namespace Kunstmaan\VotingBundle\Helper\UpDown;

use Kunstmaan\VotingBundle\Entity\UpDown\UpVote;
use Kunstmaan\VotingBundle\Helper\AbstractVotingHelper;

/**
 * Helper class for Up votes
 */
class UpVoteHelper extends AbstractVotingHelper
{
    /**
     * @var string
     */
    protected $repository = UpVote::class;
}
