<?php

namespace Kunstmaan\VotingBundle\Entity\Facebook;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\Facebook\FacebookLikeRepository")
 * @ORM\Table(name="k_voting_facebooklike")
 */
class FacebookLike extends AbstractVote
{

}