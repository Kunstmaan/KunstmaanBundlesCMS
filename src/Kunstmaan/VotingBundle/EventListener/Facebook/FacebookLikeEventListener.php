<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\EventListener\AbstractVoteListener;

class FacebookLikeEventListener extends AbstractVoteListener
{
    /**
     * @param FacebookLikeEvent $event
     */
    public function onFacebookLike(FacebookLikeEvent $event)
    {
        $this->createVote(
            new FacebookLike(),
            $event,
            $this->getActions()['facebook_like']['default_value'] ?: null
        );
    }
}
