<?php

namespace Kunstmaan\ArticleBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository class for the AbstractArticlePage
 */
abstract class AbstractArticlePageRepository extends EntityRepository
{
    /**
     * Returns an array of all article pages
     *
     * @param string $lang
     * @param int    $offset
     * @param int    $limit
     *
     * @return array
     */
    abstract public function getArticles($lang = null, $offset = 0, $limit = 10);
}
