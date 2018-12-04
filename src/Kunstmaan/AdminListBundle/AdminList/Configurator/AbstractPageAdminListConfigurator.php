<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Doctrine\DBAL\Connection;
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
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $nodeIds = [];

    /**
     * @var array
     */
    private $nodeTranslationIds = [];

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
     * {@inheritdoc}
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
            ->select('b.id, b.node_id')
            ->from('kuma_node_translations', 'b')
            ->innerJoin('b', 'kuma_nodes', 'n', 'b.node_id = n.id')
            ->where('n.deleted = 0')
            ->andWhere('n.ref_entity_name = :class')
            ->setParameter('class', $this->getPageClass())
            ->addOrderBy('b.updated', 'DESC');

        // Clone query for next step with same start query.
        $qbHelper = clone $qbQuery;
        // Get the node translations having current locale.
        $this->getCurrentLocaleResults($qbQuery);
        // Get the node translations for the other locales, excluding current locale
        $this->getOtherLocalesResults($qbHelper);

        // Make the final query.
        $queryBuilder
            ->select('b.*')
            ->from('kuma_node_translations', 'b')
            ->innerJoin('b', 'kuma_nodes', 'n', 'b.node_id = n.id')
            ->andWhere('b.id IN (:ids)')
            ->setParameter('ids', $this->nodeTranslationIds, Connection::PARAM_STR_ARRAY)
            ->orderBy('b.updated', 'DESC');
    }

    /**
     * @param QueryBuilder $qb
     */
    private function getCurrentLocaleResults(QueryBuilder $qb)
    {
        $results = $qb
            ->andWhere('b.lang = :lang')
            ->setParameter('lang', $this->locale)
            ->execute()
            ->fetchAll();

        foreach ($results as $result) {
            $this->nodeIds[] = $result['node_id'];
            $this->nodeTranslationIds[] = $result['id'];
        }
    }

    /**
     * @param QueryBuilder $qb
     */
    private function getOtherLocalesResults(QueryBuilder $qb)
    {
        $qb
            ->andWhere('b.lang != :lang')
            ->setParameter('lang', $this->locale);

        if (!empty($this->nodeIds)) {
            $qb
                ->andWhere('b.node_id NOT IN (:ids)')
                ->setParameter('ids', $this->nodeIds, Connection::PARAM_STR_ARRAY);
        }

        $results = $qb
            ->groupBy('b.node_id')
            ->execute()
            ->fetchAll();

        foreach ($results as $result) {
            $this->nodeTranslationIds[] = $result['id'];
        }
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
