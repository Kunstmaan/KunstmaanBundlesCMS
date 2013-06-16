<?php

namespace Kunstmaan\VotingBundle\Services;

use Doctrine\ORM\EntityManager;

use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;

/**
 * Helper class get repository for an event
 */
class RepositoryResolver
{

    /**
    * Constructor
    * @param Object $em entity manager
    */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
    * Return repository for event
    * @param Event $event event
    *
    * @return Repository
    */
    public function getRepositoryForEvent($event)
    {
        $repository = null;

        if ($event instanceof DownVoteEvent) {
            $repository = $this->getRepository('Kunstmaan\VotingBundle\Entity\UpDown\DownVote');
        }

        if ($event instanceof UpVoteEvent) {
            $repository = $this->getRepository('Kunstmaan\VotingBundle\Entity\UpDown\UpVote');
        }

        if ($event instanceof LinkedInShareEvent) {
            $repository = $this->getRepository('Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare');
        }

        if ($event instanceof FacebookLikeEvent) {
            $repository = $this->getRepository('Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike');
        }

        if ($event instanceof FacebookSendEvent) {
            $repository = $this->getRepository('Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend');
        }

        return $repository;
    }

    /**
    * Return a repository By name
    * @param string $name name
    *
    * @return Repository
    */
    protected function getRepository($name)
    {
        return $this->em->getRepository($name);
    }

}