<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\DateTimeFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;

/**
 * An abstract admin list configurator that can be used for pages.
 */
abstract class AbstractPageAdminListConfigurator extends AbstractDoctrineDBALAdminListConfigurator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string $locale
     */
    private $locale;

    /**
     * AbstractPageAdminListConfigurator constructor.
     *
     * @param EntityManagerInterface $em
     * @param string                 $locale
     */
    public function __construct(EntityManagerInterface $em, $locale)
    {
        parent::__construct($em->getConnection());
        $this->em = $em;
        $this->locale = $locale;
        $this->setListTemplate('KunstmaanAdminListBundle:Page:list.html.twig');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('title', 'Title', true, 'KunstmaanAdminListBundle:Page:list-title.html.twig');
        $this->addField('online', 'Online', true, 'KunstmaanNodeBundle:Admin:online.html.twig');
        $this->addField('updated', 'Updated at', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('title', new StringFilterType('title'), 'Title');
        $this->addFilter('online', new BooleanFilterType('online', 't'), 'Online');
        $this->addFilter('updated', new DateTimeFilterType('updated', 'v'), 'Updated at');
    }

    /**
     * Get the edit url for the given $item
     *
     * @param array $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return [
            'path' => 'KunstmaanNodeBundle_nodes_edit',
            'params' => ['id' => $item['node_id']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function getDeleteUrlFor($item)
    {
        return [
            'path' => 'KunstmaanNodeBundle_nodes_delete',
            'params' => ['id' => $item['node_id']],
        ];
    }

    /**
     * Get the fully qualified class name
     *
     * @return string
     */
    public function getPageClass()
    {
        return $this->em->getClassMetadata($this->getRepositoryName())->getName();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $params
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = [])
    {
        $qbQuery = clone $queryBuilder;

        $qbQuery
            ->select('nt.id, nt.title, n.id as node_id, nt.lang,
                        IF(nt.lang = \''.$this->locale.'\', nt.online, 0) as online,
                        IF(nt.lang = \''.$this->locale.'\', nt.updated, NULL) as updated,
                        IF(nt.lang = \''.$this->locale.'\', nt.created, NULL) as created')
            ->from('kuma_node_translations', 'nt')
            ->innerJoin('nt', 'kuma_nodes', 'n', 'nt.node_id = n.id')
            ->where('n.ref_entity_name = :pageClass')
            ->andWhere('n.deleted = 0')
            ->orderBy('nt.updated', 'DESC');

        /**
         * This is necessary for the results query happening in the pagerfanta adapter,
         * otherwise it will result in a groupBy with count query that results with count = 1
         * and no pagination will be available
         */
        $qbHelper = clone $queryBuilder;
        $qbHelper
            ->select('a.*')
            ->from('('.$qbQuery->getSQL().')', 'a')
            ->groupBy('a.node_id');

        $queryBuilder
            ->select('b.*')
            ->from('('.$qbHelper->getSQL().')', 'b')
            ->orderBy('FIELD(b.lang, :lang)', 'DESC')
            ->setParameter('pageClass', $this->getPageClass())
            ->setParameter('lang', $this->locale);
    }

    /**
     * Return default repository name.
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return sprintf('%s:%s\%s', $this->getBundleName(), 'Pages', $this->getEntityName());
    }

    /**
     * @return EntityInterface
     */
    abstract public function getOverviewPageClass();

    /**
     * Returns the overviewpage.
     */
    public function getOverviewPage()
    {
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository($this->getOverviewPageClass());

        $overviewPage = $repository->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $overviewPage;
    }

    /**
     * @return string
     */
    abstract public function getReadableName();
}
