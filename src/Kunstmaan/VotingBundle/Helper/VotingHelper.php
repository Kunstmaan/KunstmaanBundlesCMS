<?php

namespace Kunstmaan\VotingBundle\Helper;

use Doctrine\ORM\EntityManager;

/**
 * Helper class for Voting
 */
class VotingHelper
{

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

}