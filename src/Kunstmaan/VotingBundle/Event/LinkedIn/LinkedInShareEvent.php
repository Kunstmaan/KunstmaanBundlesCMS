<?php

namespace Kunstmaan\VotingBundle\Event\LinkedIn;

use Kunstmaan\VotingBundle\Event\AbstractVoteEvent;

/**
 * Event triggered through a callback from the LinkedIn Javascript API when a Share has been executed
 */
final class LinkedInShareEvent extends AbstractVoteEvent
{
}
