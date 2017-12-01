<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType;

/**
 * Class AbstractFilterType
 * @package Kunstmaan\AdminListBundle\AdminList\FilterType
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

