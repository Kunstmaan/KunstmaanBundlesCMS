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
     * @return array
     */
    abstract public function getArticles();
}
