<?php

namespace {{ namespace }}\Repository;

use Doctrine\ORM\Query;
use Kunstmaan\ArticleBundle\Repository\AbstractArticlePageRepository;

/**
 * Repository class for the {{ entity_class }}Page
 */
class {{ entity_class }}PageRepository extends AbstractArticlePageRepository
{
    /**
     * Returns an array of all {{ entity_class }}Pages
     *
     * @param string $lang
     * @param int    $offset
     * @param int    $limit
     *
     * @return array
     */
    public function getArticles($lang = null, $offset = null, $limit = null)
    {
        $q = $this->getArticlesQuery($lang, $offset, $limit);

        return $q->getResult();
    }

    /**
     * Returns the article query
     *
     * @param string $lang
     * @param int    $offset
     * @param int    $limit
     *
     * @return Query
     */
    public function getArticlesQuery($lang = null, $offset, $limit)
    {
	$qb = $this->createQueryBuilder('a')
	    ->innerJoin('KunstmaanNodeBundle:NodeVersion', 'v', 'WITH', 'a.id = v.refId')
	    ->innerJoin('KunstmaanNodeBundle:NodeTranslation', 't', 'WITH', 't.publicNodeVersion = v.id')
	    ->innerJoin('KunstmaanNodeBundle:Node', 'n', 'WITH', 't.node = n.id')
	    ->where('t.online = 1')
	    ->andWhere('n.deleted = 0')
	    ->andWhere('v.refEntityName = :refname')
	    ->orderBy('a.date', 'DESC')
	    ->setParameter('refname', "{{ namespace | replace({'\\': '\\\\'}) }}\\Entity\\Pages\\{{ entity_class }}Page");

	if (!is_null($lang)) {
	    $qb->andWhere('t.lang = :lang')
		->setParameter('lang', $lang);
        }

	if ($limit) {
	    $qb->setMaxResults(1);

	    if ($offset) {
		$qb->setFirstResult($offset);
            }
        }

	return $qb->getQuery();
    }
}
