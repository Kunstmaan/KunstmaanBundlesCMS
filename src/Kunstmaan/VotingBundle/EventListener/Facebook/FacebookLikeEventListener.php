<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;

class FacebookLikeEventListener
{

    public function onFacebookLike(FacebookLikeEvent $event)
    {
        $vote = new FacebookLike();
        $vote->setReference($event->getResponse());
        $vote->setIp($event->getRequest()->getClientIp());

        $em = $this->getDoctrine()->getManager();

        $em->persist($vote);
        $em->flush();
    }

}