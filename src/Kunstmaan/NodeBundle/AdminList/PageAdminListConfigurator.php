<?php

namespace Kunstmaan\NodeBundle\AdminList;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminListBundle\AdminList\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\BooleanFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

use Doctrine\ORM\QueryBuilder;

/**
 * PageAdminListConfigurator
 */
class PageAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    protected $locale;
    protected $permission;

    /**
     * @param EntityManager $em         The entity manager
     * @param AclHelper     $aclHelper  The ACL helper
     * @param string        $locale     The current locale
     * @param string        $permission The permission
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper, $locale, $permission)
    {
        parent::__construct($em, $aclHelper);
        $this->locale = $locale;
        $this->setPermissionDefinition(
            new PermissionDefinition(array($permission), 'Kunstmaan\NodeBundle\Entity\Node', 'n')
        );
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $builder = $this->getAdminListFilter();
        $builder->add('title', new StringFilter("title"), "Title");
        $builder->add('online', new BooleanFilter("online"), "Online");
        $builder->add('created', new DateFilter("created"), "Created At");
        $builder->add('updated', new DateFilter("updated"), "Updated At");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("title", "Title", true);
        $this->addField("created", "Created At", true);
        $this->addField("updated", "Updated At", true);
        $this->addField("online", "Online", true);
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanNodeBundle_pages_edit',
            'params' => array('id' => $item->getNode()->getId())
        );
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanNodeBundle_pages');
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return "";
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanNodeBundle:NodeTranslation';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id');
        $queryBuilder->andWhere('b.lang = :lang');
        $queryBuilder->andWhere('n.deleted = 0');
        $queryBuilder->setParameter('lang', $this->locale);
    }

}
