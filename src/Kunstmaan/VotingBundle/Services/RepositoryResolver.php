<?php

namespace Kunstmaan\VotingBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\VotingBundle\Event\EventInterface;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\Entity\UpDown\DownVote;
use Kunstmaan\VotingBundle\Entity\UpDown\UpVote;
use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;

/**
 * Helper class get repository for an event
 */
class RepositoryResolver
{
    /**
     * Entity manager
     */
    protected $em;

    /**
     * @param object $em entity manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Return repository for event
     *
     * @param EventInterface $event event
     *
     * @return EntityRepository
     */
    public function getRepositoryForEvent($event)
    {
        $repository = null;

        if ($event instanceof DownVoteEvent) {
            $repository = $this->getRepository(DownVote::class);
        }

        if ($event instanceof UpVoteEvent) {
            $repository = $this->getRepository(UpVote::class);
        }

        if ($event instanceof LinkedInShareEvent) {
            $repository = $this->getRepository(LinkedInShare::class);
        }

        if ($event instanceof FacebookLikeEvent) {
            $repository = $this->getRepository(FacebookLike::class);
        }

        if ($event instanceof FacebookSendEvent) {
            $repository = $this->getRepository(FacebookSend::class);
        }

        return $repository;
    }

    /**
     * Return a repository By name
     *
     * @param string $name name
     *
     * @return EntityRepository
     */
    protected function getRepository($name)
    {
        return $this->em->getRepository($name);
    }
}
