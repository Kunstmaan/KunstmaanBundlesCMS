<?php

namespace Kunstmaan\ArticleBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Repository class for the AbstractArticlePage
 */
class AbstractArticlePageRepository extends EntityRepository
{
    /**
     * Returns an array of all AbstractArticlePages
     *
     * @return array
     */
    public function getArticles()
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('Kunstmaan\ArticleBundle\Entity\AbstractArticlePage', 'qp');

        $query = "SELECT";
        $query .= " article.*";
        $query .= " FROM";
        $query .= " kuma_abstractarticles as article";
        $query .= " INNER JOIN";
        $query .= " kuma_node_versions nv ON nv.ref_id = article.id";
        $query .= " INNER JOIN";
        $query .= " kuma_node_translations nt ON nt.public_node_version_id = nv.id and nt.id = nv.node_translation_id";
        $query .= " INNER JOIN";
        $query .= " kuma_nodes n ON n.id = nt.node_id";
        $query .= " WHERE";
        $query .= " n.deleted = 0";
        $query .= " AND";
        $query .= " n.ref_entity_name = 'Kunstmaan\\\\ArticleBundle\\\\Entity\\\\AbstractArticle'";
        $query .= " AND";
        $query .= " nt.online = 1 ";
        $query .= " ORDER BY article.date DESC";

        $q = $this->_em->createNativeQuery($query, $rsm);

        return $q->getResult();
    }

}
