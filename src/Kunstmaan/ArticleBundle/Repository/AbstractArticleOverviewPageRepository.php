<?php

namespace Kunstmaan\ArticleBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->getEntityName(), 'overviewpage');
        $query = "
            SELECT
                overviewpage.*
            FROM ";
        $query .= $this->_em->getClassMetadata($this->getEntityName())->getTableName();
        $query .= " AS overviewpage
            INNER JOIN
                kuma_node_versions nv ON nv.ref_id = overviewpage.id
            INNER JOIN
                kuma_node_translations nt ON nt.public_node_version_id = nv.id AND nt.id = nv.node_translation_id
            INNER JOIN
                kuma_nodes n ON n.id = nt.node_id
            WHERE
                n.deleted = 0
            AND
                n.ref_entity_name = :entity_name
        ";
        $q = $this->_em->createNativeQuery($query, $rsm);
        $q->setParameter('entity_name',$this->getEntityName());

        return $q->getResult();
    }
}
