<?php
namespace Kunstmaan\AdminListBundle\AdminList;

class AdminListFilter
{

    /**
     * The children of the form
     * @var array
     */
    private $filterDefinitions = array();

    /* @var Filter[] */
    private $currentFilters = array();

    private $currentParameters = array();

    /**
     * @param string $columnName
     * @param string $type
     * @param string $filterName
     * @param array  $options
     *
     * @return AdminListFilter
     */
    public function add($columnName, $type = null, $filterName = null, array $options = array())
    {
        $this->filterDefinitions[$columnName] = array('type' => $type, 'options' => $options, 'filtername' => $filterName);

        return $this;
    }

    public function get($columnName)
    {
        return $this->filterDefinitions[$columnName];
    }

    public function remove($columnName)
    {
        if (isset($this->filterDefinitions[$columnName])) {
            unset($this->filterDefinitions[$columnName]);
        }

        return $this;
    }

    public function has($columnName)
    {
        return isset($this->filterDefinitions[$columnName]);
    }

    public function getFilterDefinitions()
    {
        return $this->filterDefinitions;
    }

    public function bindRequest($request)
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

    public function getCurrentParameters()
    {
        return $this->currentParameters;
    }

    public function getCurrentFilters()
    {
        return $this->currentFilters;
    }

    public function adaptQueryBuilder($querybuilder)
    {
        $expressions = array();
        foreach ($this->currentFilters as $filter) {
            $filter->adaptQueryBuilder($querybuilder, $expressions);
        }
        if (sizeof($expressions) > 0) {
            foreach ($expressions as $expression) {
                $querybuilder->andWhere($expression);
            }
        }
    }

}
