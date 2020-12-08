<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\EventListener\AbstractVoteListener;

class FacebookSendEventListener extends AbstractVoteListener
{
    public function onFacebookSend(FacebookSendEvent $event)
    {
        $this->createVote(
            new FacebookSend(),
            $event,
            $this->getActions()['facebook_send']['default_value'] ?: null
        );
    }
}
