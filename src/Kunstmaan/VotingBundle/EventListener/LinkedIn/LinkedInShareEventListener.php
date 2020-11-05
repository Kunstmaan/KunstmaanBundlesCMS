<?php

namespace Kunstmaan\VotingBundle\EventListener\LinkedIn;

use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\EventListener\AbstractVoteListener;

class LinkedInShareEventListener extends AbstractVoteListener
{
    public function onLinkedInShare(LinkedInShareEvent $event)
    {
        $this->createVote(
            new LinkedInShare(),
            $event,
            $this->getActions()['linkedin_share']['default_value'] ?: null
        );
    }
}
