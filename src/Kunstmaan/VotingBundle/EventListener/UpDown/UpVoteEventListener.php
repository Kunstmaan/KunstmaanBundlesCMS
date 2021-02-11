<?php

namespace Kunstmaan\VotingBundle\EventListener\UpDown;

use Kunstmaan\VotingBundle\Entity\UpDown\UpVote;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\EventListener\AbstractVoteListener;

class UpVoteEventListener extends AbstractVoteListener
{
    public function onUpVote(UpVoteEvent $event)
    {
        $this->createVote(
            new UpVote(),
            $event,
            $this->getActions()['up_vote']['default_value'] ?: null
        );
    }
}
