<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Doctrine\ORM\QueryBuilder;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

abstract class AbstractDoctrineORMAdminListConfigurator extends AbstractAdminListConfigurator
{
    /* @var EntityManager $em */
    private $em;

    /* @var Query $query */
    private $query = null;

    /* @var Pagerfanta $pagerfanta */
    private $pagerfanta = null;

    /* @var PermissionDefinition $permissionDef */
    private $permissionDef = null;

    /* @var AclHelper $aclHelper */
    private $aclHelper = null;

    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        $this->em = $em;
        $this->aclHelper = $aclHelper;
    }

    /**
     * Return the url to edit the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'	 => $this->getPathByConvention($this::SUFFIX_EDIT),
            'params' => array('id' => $item->getId())
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
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params'	=> array('id' => $item->getId())
        );
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        if (is_null($this->pagerfanta)) {
            $adapter = new DoctrineORMAdapter($this->getQuery());
            $this->pagerfanta = new Pagerfanta($adapter);
            $this->pagerfanta->setCurrentPage($this->getPage());
            $this->pagerfanta->setMaxPerPage($this->getLimit());
        }

        return $this->pagerfanta;
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->where('1=1');
    }

    public function getCount()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    public function getItems()
    {
        return $this->getPagerfanta()->getCurrentPageResults();
    }

    public function getQuery()
    {
        if (is_null($this->query)) {
            $queryBuilder = $this->em->getRepository($this->getRepositoryName())->createQueryBuilder('b');
            $this->adaptQueryBuilder($queryBuilder);

            // Apply filters
            $filters = $this->getAdminListFilter()->getCurrentFilters();
            foreach ($filters as $filter) {
                $filter->getType()->setQueryBuilder($queryBuilder);
                $filter->getType()->apply($filter->getData(), $filter->getUniqueId());
            }

            // Apply sorting
            if (!empty($this->orderBy)) {
                $orderBy = $this->orderBy;
                if (!strpos($orderBy, '.')) {
                    $orderBy = 'b.' . $orderBy;
                }
                $queryBuilder->orderBy($orderBy, ($this->orderDirection == 'DESC' ? 'DESC' : 'ASC'));
            }

            // Apply ACL restrictions (if applicable)
            if (!is_null($this->permissionDef) && !is_null($this->aclHelper)) {
                $this->query = $this->aclHelper->apply($queryBuilder, $this->permissionDef);
            } else {
                $this->query = $queryBuilder->getQuery();
            }
        }

        return $this->query;
    }

    /**
     * Get current permission definition.
     *
     * @return PermissionDefinition|null
     */
    public function getPermissionDefinition()
    {
        return $this->permissionDef;
    }

    /**
     * Set permission definition.
     *
     * @param PermissionDefinition $permissionDef
     *
     * @return AbstractAdminListConfigurator|AbstractDoctrineORMAdminListConfigurator
     */
    public function setPermissionDefinition(PermissionDefinition $permissionDef)
    {
        $this->permissionDef = $permissionDef;

        return $this;
    }

}
