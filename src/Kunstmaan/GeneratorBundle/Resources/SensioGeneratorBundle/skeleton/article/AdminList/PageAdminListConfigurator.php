<?php

namespace {{ namespace }}\AdminList;

use {{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage;
use {{ namespace }}\Entity\Pages\{{ entity_class }}Page;
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

    public function adaptQueryBuilder(QueryBuilder $queryBuilder): void
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->setParameter('class', {{ entity_class }}Page::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getOverviewPageRepository()
    {
        return $this->em->getRepository({{ entity_class }}OverviewPage::class);
    }

    public function getListTemplate(): string
    {
        return 'AdminList/{{ entity_class }}PageAdminList/list.html.twig';
    }
}
