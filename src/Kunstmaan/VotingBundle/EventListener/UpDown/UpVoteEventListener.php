<?php

namespace Kunstmaan\VotingBundle\EventListener\UpDown;

use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Entity\UpDown\UpVote;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Symfony\Component\DependencyInjection\Container;

class UpVoteEventListener
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

    public function onUpVote(UpVoteEvent $event)
    {
        $vote = new UpVote();
        $vote->setReference($event->getReference());
        if (!is_null($event->getRequest())) {
            $vote->setIp($event->getRequest()->getClientIp());
        }
        if ($event->getValue() !== null) {
            $vote->setValue($event->getValue());
        } else {
            $actions = $this->container->getParameter('kuma_voting.actions');
            if (isset($actions['up_vote'])) {
                $vote->setValue($actions['up_vote']['default_value']);
            }
        }
        $this->em->persist($vote);
        $this->em->flush();
    }

}
