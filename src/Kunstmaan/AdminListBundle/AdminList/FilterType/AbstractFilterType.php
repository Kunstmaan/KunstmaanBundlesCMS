<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType;

use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminListBundle\AdminList\FilterType\FilterTypeInterface;

/**
 * AbstractFilterType
 *
 * Abstract base class for all admin list filters
 */
abstract class AbstractFilterType implements FilterTypeInterface
{
    /**
     * @var null|string
     */
    protected $columnName = null;

    /**
     * @var null|string
     */
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
    abstract public function bindRequest(Request $request, array &$data, $uniqueId);

    /**
     * @param array  $data     Data
     * @param string $uniqueId The identifier
     */
    abstract public function apply(array $data, $uniqueId);

    /**
     * @return string
     */
    abstract public function getTemplate();


    /**
     * Returns empty string if no alias, otherwise make sure the alias has just one '.' after it.
     *
     * @return string
     */
    protected function getAlias()
    {
        if (empty($this->alias)) {
            return '';
        }

        if (strpos($this->alias, '.') !== false) {
            return $this->alias;
        }

        return $this->alias . '.';
    }
}
