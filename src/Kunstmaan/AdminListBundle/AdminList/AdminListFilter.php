<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\CircularReferenceException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminListFilter
{

    /**
     * The children of the form
     * @var array
     */
    private $filterdefinitions = array();

    private $currentfilters = array();

    private $currentparameters = array();

    public function add($colname, $type = null, $filtername = null, array $options = array())
    {
        $this->filterdefinitions[$colname] = array('type' => $type, 'options' => $options, 'filtername' => $filtername);
        return $this;
    }

    public function get($colname)
    {
        return $this->filterdefinitions[$colname];
    }

    public function remove($colname)
    {
        if (isset($this->filterdefinitions[$colname])) {
            unset($this->filterdefinitions[$colname]);
        }
        return $this;
    }

    public function has($colname)
    {
        return isset($this->filterdefinitions[$colname]);
    }

    public function getFilterdefinitions()
    {
        return $this->filterdefinitions;
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
                $this->currentfilters[] = $filter;
                $filter->bindRequest($request);
                $index++;
            }
        }
    }

    public function getCurrentparameters()
    {
        return $this->currentparameters;
    }

    public function getCurrentfilters()
    {
        return $this->currentfilters;
    }

    public function adaptQueryBuilder(\Doctrine\ORM\QueryBuilder $querybuilder)
    {
        $expressions = array();
        foreach ($this->currentfilters as $filter) {
            $filter->adaptQueryBuilder($querybuilder, $expressions);
        }
        if (sizeof($expressions) > 0) {
            foreach ($expressions as $expression) {
                $querybuilder->andWhere($expression);
            }
        }
    }

}
