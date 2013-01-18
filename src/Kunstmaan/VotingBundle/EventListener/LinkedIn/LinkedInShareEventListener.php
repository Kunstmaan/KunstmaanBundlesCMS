<?php

namespace Kunstmaan\VotingBundle\EventListener\LinkedIn;

use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;

class LinkedInShareEventListener
{

    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onLinkedInShare(LinkedInShareEvent $event)
    {
        $vote = new LinkedInShare();
        $vote->setReference($event->getReference());
        $vote->setIp($event->getRequest()->getClientIp());

        if ($event->getValue() != null) {
            $vote->setValue($event->getValue());
        }

        $this->em->persist($vote);
        $this->em->flush();
    }

}