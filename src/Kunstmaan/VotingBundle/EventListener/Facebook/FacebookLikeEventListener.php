<?php

namespace Kunstmaan\VotingBundle\EventListener\Facebook;

use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;
use Symfony\Component\DependencyInjection\Container;

class FacebookLikeEventListener
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function onFacebookLike(FacebookLikeEvent $event)
    {
        $vote = new FacebookLike();
        $vote->setReference($event->getResponse());
        if (!is_null($event->getRequest())) {
            $vote->setIp($event->getRequest()->getClientIp());
        }
        if ($event->getValue() !== null) {
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
