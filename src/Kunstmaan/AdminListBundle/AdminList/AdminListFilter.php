<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminListFilter
{

    /**
     * The children of the form
     * @var array
     */
    private $filterDefinitions = array();

    /**
     * @var Filter[]
     */
    private $currentFilters = array();

    private $currentparameters = array();

    /**
     * @param       $colname
     * @param null  $type
     * @param null  $filtername
     * @param array $options
     *
     * @return AdminListFilter
     */
    public function add($colname, $type = null, $filtername = null, array $options = array())
    {
        $this->filterDefinitions[$colname] = array('type' => $type, 'options' => $options, 'filtername' => $filtername);

        return $this;
    }

    public function get($colname)
    {
        return $this->filterDefinitions[$colname];
    }

    public function remove($colname)
    {
        if (isset($this->filterDefinitions[$colname])) {
            unset($this->filterDefinitions[$colname]);
        }
        return $this;
    }

    public function has($colname)
    {
        return isset($this->filterDefinitions[$colname]);
    }

    public function getFilterDefinitions()
    {
        return $this->filterDefinitions;
    }

    public function bindRequest($request)
    {
        $this->currentparameters = $request->query->all();
        $filter_columnnames = $request->query->get('filter_columnname');
        if (isset($filter_columnnames)) {
            $uniqueids = $request->query->get('filter_uniquefilterid');
            $index = 0;
            foreach ($filter_columnnames as $filter_columnname) {
                $uniqueid = $uniqueids[$index];
                $filter = new Filter($filter_columnname, $this->get($filter_columnname), $uniqueid);
                $this->currentFilters[] = $filter;
                $filter->bindRequest($request);
                $index++;
            }
        }
    }

    public function getCurrentparameters()
    {
        return $this->currentparameters;
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
