<?php

namespace {{ namespace }}\AdminList\{{ entity_class }};

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;
use {{ namespace }}\Entity\{{ entity_class }}\{{ entity_class }}OverviewPage;

/**
 * The AdminList configurator for the {{ entity_class }}Page
 */
class {{ entity_class }}PageAdminListConfigurator extends AbstractArticlePageAdminListConfigurator
{
    /**
     * Return current bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return '{{ bundle.getName() }}';
    }

    /**
     * Return current entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return '{{ entity_class }}\{{ entity_class }}Page';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->setParameter('class', '{{ namespace }}\Entity\{{ entity_class }}\{{ entity_class }}Page');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getOverviewPageRepository()
    {
        return $this->em->getRepository('{{ bundle.getName() }}:{{ entity_class }}\{{ entity_class }}OverviewPage');
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return '{{ bundle.getName() }}:AdminList/{{ entity_class }}/{{ entity_class }}PageAdminList:list.html.twig';
    }
}
