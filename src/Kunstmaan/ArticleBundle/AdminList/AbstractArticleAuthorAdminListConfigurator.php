<?php

namespace Kunstmaan\ArticleBundle\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;

/**
 * The AdminList configurator for the AbstractArticleAuthor
 */
class AbstractArticleAuthorAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $permission;

    /**
     * @param string $locale The current locale
     */
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper, $locale)
    {
        parent::__construct($em, $aclHelper);
        $this->locale = $locale;
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

        return 'AbstractArticleAuthor';
    }

    public function getEntityClass(): string
    {
        return AbstractAuthor::class;
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('name', new StringFilterType('name'), 'article.author.list.filter.name');
        $this->addFilter('link', new StringFilterType('link'), 'article.author.list.filter.link');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'article.author.list.header.name', true);
        $this->addField('link', 'article.author.list.header.link', true);
    }
}
