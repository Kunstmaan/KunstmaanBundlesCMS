<?php

namespace Kunstmaan\VotingBundle\Event\Facebook;

use Kunstmaan\VotingBundle\Event\AbstractVoteEvent;

/**
 * Event triggered through a callback from the Facebook API when a Send has been executed
 */
class FacebookSendEvent extends AbstractVoteEvent
{

}
