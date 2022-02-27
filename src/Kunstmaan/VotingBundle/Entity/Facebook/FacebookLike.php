<?php

namespace Kunstmaan\VotingBundle\Entity\Facebook;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;
use Kunstmaan\VotingBundle\Repository\Facebook\FacebookLikeRepository;

/**
 * A Facebook Like Event
 *
 * The reference will hold the URL that has been liked
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\Facebook\FacebookLikeRepository")
 * @ORM\Table(name="kuma_voting_facebooklike")
 */
#[ORM\Entity(repositoryClass: FacebookLikeRepository::class)]
#[ORM\Table(name: 'kuma_voting_facebooklike')]
class FacebookLike extends AbstractVote
{
}
