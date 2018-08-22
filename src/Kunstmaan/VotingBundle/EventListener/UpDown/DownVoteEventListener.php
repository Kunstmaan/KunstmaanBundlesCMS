<?php

namespace Kunstmaan\VotingBundle\EventListener\UpDown;

use Kunstmaan\VotingBundle\Entity\UpDown\DownVote;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\EventListener\AbstractVoteListener;

class DownVoteEventListener extends AbstractVoteListener
{
    /**
     * @param DownVoteEvent $event
     */
    public function onDownVote(DownVoteEvent $event)
    {
        $this->createVote(
            new DownVote(),
            $event,
            $this->getActions()['down_vote']['default_value'] ?: null
        );
    }
}
