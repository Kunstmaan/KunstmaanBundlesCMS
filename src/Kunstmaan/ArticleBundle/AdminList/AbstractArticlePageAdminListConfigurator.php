<?php

namespace Kunstmaan\ArticleBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

/**
 * The AdminList configurator for the AbstractArticlePage
 */
abstract class AbstractArticlePageAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
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
     * Return current bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return "KunstmaanArticleBundle";
    }

    /**
     * Return current entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return "AbstractArticlePage";
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('title', new StringFilterType('title'), 'Title')
            ->addFilter('online', new BooleanFilterType('online'), 'Online')
            ->addFilter('created', new DateFilterType('created', 'nv'), 'Created At')
            ->addFilter('updated', new DateFilterType('updated', 'nv'), 'Updated At');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('title', 'Title', true, 'KunstmaanNodeBundle:Admin:title.html.twig')
            ->addField('created', 'Created At', true)
            ->addField('updated', 'Updated At', true)
            ->addField('online', 'Online', true, 'KunstmaanNodeBundle:Admin:online.html.twig');
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $queryBuilder = $this->em
            ->getRepository('KunstmaanNodeBundle:NodeTranslation')
            ->createQueryBuilder('b');

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id');
        $queryBuilder->innerJoin('b.nodeVersions', 'nv', 'WITH', 'b.publicNodeVersion = nv.id');
        $queryBuilder->andWhere('b.lang = :lang');
        $queryBuilder->andWhere('n.deleted = 0');
        $queryBuilder->andWhere('n.refEntityName = :class');
        $queryBuilder->addOrderBy("b.updated", "DESC");
        $queryBuilder->setParameter('lang', $this->locale);
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        /* @var Node $node */
        $node = $item->getNode();

        return array(
            'path'   => 'KunstmaanNodeBundle_nodes_edit',
            'params' => array('id' => $node->getId())
        );
    }

    /**
     * Get the delete url for the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        /* @var Node $node */
        $node = $item->getNode();

        return array(
            'path'   => 'KunstmaanNodeBundle_nodes_delete',
            'params' => array('id' => $node->getId())
        );
    }

    /**
     * Returns the OverviewPage of these articles
     *
     * @return AbstractArticleOverviewPage
     */
    public function getOverviewPage()
    {
        $repository = $this->getOverviewPageRepository();
        $pages = $repository->findActiveOverviewPages();

        if (isset($pages) && count($pages) > 0) {
            return $pages[0];
        }

        return null;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    abstract public function getOverviewPageRepository();

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return 'KunstmaanArticleBundle:AbstractArticlePageAdminList:list.html.twig';
    }
}
