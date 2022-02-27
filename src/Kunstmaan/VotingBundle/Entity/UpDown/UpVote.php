<?php

namespace Kunstmaan\VotingBundle\Entity\UpDown;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;
use Kunstmaan\VotingBundle\Repository\UpDown\UpVoteRepository;

/**
 * A standard up vote
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\UpDown\UpVoteRepository")
 * @ORM\Table(name="kuma_voting_upvote")
 */
#[ORM\Entity(repositoryClass: UpVoteRepository::class)]
#[ORM\Table(name: 'kuma_voting_upvote')]
class UpVote extends AbstractVote
{
}
