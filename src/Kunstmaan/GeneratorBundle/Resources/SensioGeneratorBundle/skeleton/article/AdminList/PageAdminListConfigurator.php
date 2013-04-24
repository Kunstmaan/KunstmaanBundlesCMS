<?php

namespace {{ namespace }}\AdminList\{{ entity_class }};

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;
use {{ namespace }}\Entity\Pages\{{ entity_class }}\{{ entity_class }}OverviewPage;

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
        return "{{ bundle.getName() }}";
    }

    /**
     * Return current entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return "{{ entity_class }}Page";
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->setParameter('class', '{{ namespace }}\Entity\Pages\{{ entity_class }}\{{ entity_class }}Page');
    }

    /**
     * Returns the OverviewPage of the {{ entity_class }}Pages
     *
     * @return {{ entity_class }}OverviewPage
     */
    public function getOverviewPage()
    {
        $repository = $this->em->getRepository('{{ bundle.getName() }}:Pages\{{ entity_class }}\{{ entity_class }}OverviewPage');
        $pages = $overviewpage = $repository->findAll();
        if (isset($pages) and count($pages) > 0) {
            return $pages[0];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getListTemplate()
{
    return '{{ bundle.getName() }}:AdminList/{{ entity_class }}PageAdminList:list.html.twig';
}

}
