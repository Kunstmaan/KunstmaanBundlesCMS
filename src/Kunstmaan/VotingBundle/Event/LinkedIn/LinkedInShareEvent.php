<?php

namespace Kunstmaan\VotingBundle\Event\LinkedIn;

use Kunstmaan\VotingBundle\Event\AbstractVoteEvent;

/**
 * Event triggered through a callback from the LinkedIn Javascript API when a Share has been executed
 *
 * @final since 5.9
 */
class LinkedInShareEvent extends AbstractVoteEvent
{
}
