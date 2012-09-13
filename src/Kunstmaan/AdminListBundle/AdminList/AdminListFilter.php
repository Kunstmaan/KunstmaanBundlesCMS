<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\HttpFoundation\Request;

class AdminListFilter
{

    /* @var array */
    private $filterDefinitions = array();

    /* @var Filter[] */
    private $currentFilters = array();

    /* @var array */
    private $currentParameters = array();

    /**
     * @param string $columnName
     * @param Filter $type
     * @param string $filterName
     * @param array  $options
     *
     * @return AdminListFilter
     */
    public function add($columnName, Filter $type = null, $filterName = null, array $options = array())
    {
        $this->filterDefinitions[$columnName] = array('type' => $type, 'options' => $options, 'filtername' => $filterName);

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return mixed
     */
    public function get($columnName)
    {
        return $this->filterDefinitions[$columnName];
    }

    /**
     * @param string $columnName
     *
     * @return AdminListFilter
     */
    public function remove($columnName)
    {
        if (isset($this->filterDefinitions[$columnName])) {
            unset($this->filterDefinitions[$columnName]);
        }

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function has($columnName)
    {
        return isset($this->filterDefinitions[$columnName]);
    }

    /**
     * @return array
     */
    public function getFilterDefinitions()
    {
        return $this->filterDefinitions;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->currentParameters = $request->query->all();
        $filterColumnNames = $request->query->get('filter_columnname');
        if (isset($filterColumnNames)) {
            $uniqueIds = $request->query->get('filter_uniquefilterid');
            $index = 0;
            foreach ($filterColumnNames as $filterColumnName) {
                $uniqueId = $uniqueIds[$index];
                $filter = new Filter($filterColumnName, $this->get($filterColumnName), $uniqueId);
                $this->currentFilters[] = $filter;
                $filter->bindRequest($request);
                $index++;
            }
        }
    }

    /**
     * @return array
     */
    public function getCurrentParameters()
    {
        return $this->currentParameters;
    }

    /**
     * @return Filter[]
     */
    public function getCurrentFilters()
    {
        return $this->currentFilters;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder|\Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder($queryBuilder)
    {
        $expressions = array();
        foreach ($this->currentFilters as $filter) {
            $filter->adaptQueryBuilder($queryBuilder, $expressions);
        }
        if (sizeof($expressions) > 0) {
            foreach ($expressions as $expression) {
                $queryBuilder->andWhere($expression);
            }
        }
    }

}
