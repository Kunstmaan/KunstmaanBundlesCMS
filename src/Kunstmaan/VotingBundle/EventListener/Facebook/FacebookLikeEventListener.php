<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;

class FacebookLikeEventListener
{

    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onFacebookLike(FacebookLikeEvent $event)
    {
        $vote = new FacebookLike();
        $vote->setReference($event->getResponse());
        $vote->setIp($event->getRequest()->getClientIp());

        if ($event->getValue() != null) {
            $vote->setValue($event->getValue());
        }

        $this->em->persist($vote);
        $this->em->flush();
    }

}