<?php

namespace Kunstmaan\ArticleBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

/**
 * Repository class for the AbstractArticleOverviewPage
 */
abstract class AbstractArticleOverviewPageRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findActiveOverviewPages()
    {
        $qb = $this->createQueryBuilder('a')
        ->innerJoin(NodeVersion::class, 'v', 'WITH', 'a.id = v.refId')
        ->innerJoin(NodeTranslation::class, 't', 'WITH', 't.publicNodeVersion = v.id')
        ->innerJoin(Node::class, 'n', 'WITH', 't.node = n.id')
        ->where('n.deleted = :deleted')
        ->setParameter('deleted', false)
        ->andWhere('v.refEntityName = :refname')
        ->setParameter('refname', $this->getEntityName());

        return $qb->getQuery()->getResult();
    }
}
