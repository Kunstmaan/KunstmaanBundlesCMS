<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;

class {{ entity_class }}PageAdminListConfigurator extends AbstractArticlePageAdminListConfigurator
{
    public function getBundleName(): string
    {
        return '{{ bundle.getName() }}';
    }

    public function getEntityName(): string
    {
        return 'Pages\{{ entity_class }}Page';
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->setParameter('class', '{{ namespace }}\Entity\Pages\{{ entity_class }}Page');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getOverviewPageRepository()
    {
        return $this->em->getRepository('{{ bundle.getName() }}:Pages\{{ entity_class }}OverviewPage');
    }

    public function getListTemplate(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}AdminList/{{ entity_class }}PageAdminList{% if not isV4 %}:{% else %}/{% endif %}list.html.twig';
    }
}
