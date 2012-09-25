<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

/**
 * AbstractFilter
 *
 * Abstract base class for all admin list filters
 */
abstract class AbstractFilter implements AdminListFilterInterface
{
    protected $columnName = null;
    protected $alias = null;

    /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function __construct($columnName, $alias = 'b')
    {
        $this->columnName = $columnName;
        $this->alias      = $alias;
    }

    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    abstract public function bindRequest(Request $request, &$data, $uniqueId);

    /**
     * @param ProviderInterface $provider The provider
     * @param array             $data     Data
     * @param string            $uniqueId The identifier
     */
    abstract public function apply(ProviderInterface $provider, $data, $uniqueId);

    /**
     * @return string
     */
    abstract public function getTemplate();
}
