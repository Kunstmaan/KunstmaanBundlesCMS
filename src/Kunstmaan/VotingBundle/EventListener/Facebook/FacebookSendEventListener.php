<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;

class FacebookSendEventListener
{

    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onFacebookSend(FacebookSendEvent $event)
    {
        $vote = new FacebookSend();
        $vote->setReference($event->getResponse());
        $vote->setIp($event->getRequest()->getClientIp());

        if ($event->getValue() != null) {
            $vote->setValue($event->getValue());
        }

        $this->em->persist($vote);
        $this->em->flush();
    }

}