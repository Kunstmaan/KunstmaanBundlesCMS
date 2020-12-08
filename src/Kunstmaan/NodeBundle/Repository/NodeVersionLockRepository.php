<?php

namespace Kunstmaan\NodeBundle\Repository;

use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersionLock;

class NodeVersionLockRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Check if there is a nodetranslation lock that's not passed the 30 minute threshold.
     *
     * @param bool     $isPublicVersion
     * @param int      $threshold
     * @param BaseUser $userToExclude
     *
     * @return NodeVersionLock[]
     */
    public function getLocksForNodeTranslation(NodeTranslation $nodeTranslation, $isPublicVersion, $threshold, BaseUser $userToExclude = null)
    {
        $qb = $this->createQueryBuilder('nvl')
            ->select('nvl')
            ->where('nvl.nodeTranslation = :nt')
            ->andWhere('nvl.publicVersion = :pub')
            ->andWhere('nvl.createdAt > :date')
            ->setParameter('nt', $nodeTranslation)
            ->setParameter('pub', $isPublicVersion)
            ->setParameter('date', new \DateTime(sprintf('-%s seconds', $threshold)))
        ;

        if (!\is_null($userToExclude)) {
            $qb->andWhere('nvl.owner <> :owner')
                ->setParameter('owner', $userToExclude->getUsername())
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get locks that are passed the threshold.
     *
     * @param int $threshold
     *
     * @return mixed
     */
    public function getExpiredLocks(NodeTranslation $nodeTranslation, $threshold)
    {
        $qb = $this->createQueryBuilder('nvl')
            ->select('nvl')
            ->where('nvl.nodeTranslation = :nt')
            ->andWhere('nvl.createdAt < :date')
            ->setParameter('nt', $nodeTranslation)
            ->setParameter('date', new \DateTime(sprintf('-%s seconds', $threshold)));

        return $qb->getQuery()->getResult();
    }
}
