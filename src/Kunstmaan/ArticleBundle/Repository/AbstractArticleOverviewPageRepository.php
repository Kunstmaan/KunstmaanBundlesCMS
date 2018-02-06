<?php

namespace Kunstmaan\ArticleBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
	    ->innerJoin('KunstmaanNodeBundle:NodeVersion', 'v', 'WITH', 'a.id = v.refId')
	    ->innerJoin('KunstmaanNodeBundle:NodeTranslation', 't', 'WITH', 't.publicNodeVersion = v.id')
	    ->innerJoin('KunstmaanNodeBundle:Node', 'n', 'WITH', 't.node = n.id')
	    ->where('n.deleted = 0')
	    ->andWhere('v.refEntityName = :refname')
	    ->setParameter('refname', $this->getEntityName());

	return $qb->getQuery()->getResult();
    }
}
