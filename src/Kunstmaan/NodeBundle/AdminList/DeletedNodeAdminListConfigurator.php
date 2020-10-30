<?php

namespace Kunstmaan\NodeBundle\AdminList;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

class DeletedNodeAdminListConfigurator extends NodeAdminListConfigurator
{
    public function buildFilters()
    {
        $this
            ->addFilter('title', new StringFilterType('title'), 'kuma_node.admin.list.filter.title')
            ->addFilter('created', new DateFilterType('created'), 'kuma_node.admin.list.filter.created_at')
            ->addFilter('updated', new DateFilterType('updated'), 'kuma_node.admin.list.filter.updated_at')
        ;
    }

    public function buildFields()
    {
        $this
            ->addField('title', 'kuma_node.admin.list.header.title', true, '@KunstmaanNode/Admin/title.html.twig')
            ->addField('created', 'kuma_node.admin.list.header.created_at', true)
            ->addField('updated', 'kuma_node.admin.list.header.updated_at', true)
        ;
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->setParameter('deleted', 1);
    }
}
