<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;

class FacebookSendEventListener
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function onFacebookSend(FacebookSendEvent $event)
    {
        $vote = new FacebookSend();
        $vote->setReference($event->getResponse());
        if (!is_null($event->getRequest())) {
            $vote->setIp($event
                ->getRequest()
                ->getClientIp());
        }
        if ($event->getValue() != null) {
            $vote->setValue($event->getValue());
        } else {
            $actions = $this->container->getParameter('kuma_voting.actions');
            if (isset($actions['facebook_send'])) {
                $vote->setValue($actions['facebook_send']['default_value']);
            }
        }
        $this->em->persist($vote);
        $this->em->flush();
    }

}