<?php

namespace Kunstmaan\VotingBundle\Event\Facebook;

use Kunstmaan\VotingBundle\Event\AbstractVoteEvent;

/**
 * Event triggered through a callback from the Facebook API when a Like has been executed
 */
class FacebookLikeEvent extends AbstractVoteEvent
{

}
