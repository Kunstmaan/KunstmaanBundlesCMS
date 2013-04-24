<?php

namespace {{ namespace }}\Repository\Pages\{{ entity_class }};

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Kunstmaan\ArticleBundle\Repository\AbstractArticlePageRepository;

/**
 * Repository class for the {{ entity_class }}Page
 */
class {{ entity_class }}PageRepository extends AbstractArticlePageRepository
{
    /**
     * Returns an array of all {{ entity_class }}Pages
     *
     * @return array
     */
    public function getArticles()
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('{{ namespace }}\Entity\Pages\{{ entity_class }}\{{ entity_class }}Page', 'qp');

        $query = "SELECT";
        $query .= " article.*";
        $query .= " FROM";
        $query .= " {{ prefix }}{{ entity_class|lower }}pages as article";
        $query .= " INNER JOIN";
        $query .= " kuma_node_versions nv ON nv.ref_id = article.id";
        $query .= " INNER JOIN";
        $query .= " kuma_node_translations nt ON nt.public_node_version_id = nv.id and nt.id = nv.node_translation_id";
        $query .= " INNER JOIN";
        $query .= " kuma_nodes n ON n.id = nt.node_id";
        $query .= " WHERE";
        $query .= " n.deleted = 0";
        $query .= " AND";
        $query .= " n.ref_entity_name = '{{ namespace | replace('\\', '\\\\') }}\\\\Entity\\\\Pages\\\\{{ entity_class }}\\\\{{ entity_class }}Page'";
        $query .= " AND";
        $query .= " nt.online = 1 ";
        //$query .= " ORDER BY article.date DESC";

        $q = $this->_em->createNativeQuery($query, $rsm);

        return $q->getResult();
    }

}
