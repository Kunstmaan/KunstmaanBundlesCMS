<?php

namespace Kunstmaan\ArticleBundle\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\ArticleBundle\Entity\AbstractCategory;

/**
 * The AdminList configurator for the AbstractArticleAuthor
 */
class AbstractArticleCategoryAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper)
    {
        parent::__construct($em, $aclHelper);
    }

    /**
     * @deprecated since 6.4. Use the `getEntityClass` method instead.
     *
     * Return current bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        trigger_deprecation('kunstmaan/article-bundle', '6.4', 'Method "%s" deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.', __METHOD__);

        return 'KunstmaanArticleBundle';
    }

    /**
     * @deprecated since 6.4. Use the `getEntityClass` method instead.
     *
     * Return current entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        trigger_deprecation('kunstmaan/article-bundle', '6.4', 'Method "%s" deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.', __METHOD__);

        return 'AbstractArticleCategory';
    }

    public function getEntityClass(): string
    {
        return AbstractCategory::class;
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('name', new StringFilterType('name'), 'article.category.list.filter.name');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'article.category.list.header.name', true);
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->orderBy('b.name', 'ASC');
    }

    /**
     * Overwrite the parent function. By adding the TranslationWalker, we can order by the translated fields.
     *
     * @return \Doctrine\ORM\Query|null
     */
    public function getQuery()
    {
        $query = parent::getQuery();

        if (!\is_null($query)) {
            $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            );
            $query->useQueryCache(false);
        }

        return $query;
    }
}
