<?php

namespace Kunstmaan\TaggingBundle\Entity;

use DoctrineExtensions\Taggable\TagManager as BaseTagManager;
use DoctrineExtensions\Taggable\Taggable as BaseTaggable;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Kunstmaan\NodeBundle\Entity\AbstractPage;

class TagManager extends BaseTagManager
{

    const TAGGING_HYDRATOR = 'taggingHydrator';

    public function loadTagging(BaseTaggable $resource)
    {
        if ($resource instanceof LazyLoadingTaggableInterface) {
            $resource->setTagLoader(function (Taggable $taggable) {
                parent::loadTagging($taggable);
            });

            return ;
        }

        parent::loadTagging($resource);
    }

    public function saveTagging(BaseTaggable $resource)
    {
        $tags = clone $resource->getTags();
        parent::saveTagging($resource);
        if (sizeof($tags) !== sizeof($resource->getTags())) {
            // parent::saveTagging uses getTags by reference and removes elements, so it ends up empty :-/
            // this causes all tags to be deleted when an entity is persisted more than once in a request
            // Restore:
            $this->replaceTags($tags->toArray(), $resource);
        }

    }


    /**
     * Gets all tags for the given taggable resource
     *
     * @param BaseTaggable $resource Taggable resource
     * @return array
     */
    public function getTagging(BaseTaggable $resource)
    {
        $em = $this->em;

        $config = $em->getConfiguration();
        if (is_null($config->getCustomHydrationMode(self::TAGGING_HYDRATOR))) {
            $config->addCustomHydrationMode(self::TAGGING_HYDRATOR, 'Doctrine\ORM\Internal\Hydration\ObjectHydrator');
        }

        return $em
            ->createQueryBuilder()

            ->select('t')
            ->from($this->tagClass, 't')

            ->innerJoin('t.tagging', 't2', Expr\Join::WITH, 't2.resourceId = :id AND t2.resourceType = :type')
            ->setParameter('id', $resource->getTaggableId())
            ->setParameter('type', $resource->getTaggableType())

            ->getQuery()
            ->getResult(self::TAGGING_HYDRATOR);
    }

    public function findById($id)
    {

        if (!isset($id) || is_null($id)) {
            return NULL;
        }
        $builder = $this->em->createQueryBuilder();

        $tag = $builder
            ->select('t')
            ->from($this->tagClass, 't')

            ->where($builder->expr()->eq('t.id', $id))

            ->getQuery()
            ->getOneOrNullResult();

        return $tag;
    }

    public function findAll()
    {
        $tagsRepo = $this->em->getRepository('KunstmaanTaggingBundle:Tag');

        return $tagsRepo->findAll();
    }

    public function findRelatedItems(Taggable $item, $class, $locale, $nbOfItems=1)
    {
        $instance = new $class();
        if (!($instance instanceof Taggable)) {
            return NULL;
        }

        $em = $this->em;
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata($class, 'i');

        $meta = $em->getClassMetadata($class);
        $tableName = $meta->getTableName();

        $escapedClass = str_replace('\\', '\\\\', $class);

        $query = <<<EOD
            SELECT i.*, COUNT(i.id) as number
            FROM {$tableName} i
            LEFT JOIN kuma_taggings t
            ON t.resource_id = i.id
            AND t.resource_type = '{$instance->getTaggableType()}'
            WHERE t.tag_id IN (
                SELECT tg.tag_id
                FROM kuma_taggings tg
                WHERE tg.resource_id = {$item->getId()}
                AND tg.resource_type = '{$item->getTaggableType()}'
            )
            AND i.id <> {$item->getId()}
EOD;

        if ($item instanceof AbstractPage) {
            $query .= <<< EOD
                AND i.id IN (
                    SELECT nodeversion.refId
                    FROM kuma_nodes as node
                    INNER JOIN kuma_node_translations as nodetranslation
                    ON node.id = nodetranslation.node
                    AND nodetranslation.lang = '{$locale}'
                    INNER JOIN kuma_node_versions as nodeversion
                    ON nodetranslation.publicNodeVersion = nodeversion.id
                    AND nodeversion.refEntityname = '{$escapedClass}'
                    AND node.deleted = 0
                    AND nodetranslation.online = 1
                )
EOD;
        }

        $query .= <<<EOD
            GROUP BY
                i.id
            HAVING
                number > 0
            ORDER BY
                number DESC
            LIMIT {$nbOfItems};
EOD;

        $items = $em->createNativeQuery($query, $rsm)->getResult();

        return $items;
    }

}
