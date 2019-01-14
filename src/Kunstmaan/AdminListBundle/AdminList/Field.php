<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Field
 */
class Field
{
    /**
     * @var string
     */
    private $header;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $sort;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @var null|FieldAlias
     */
    private $alias;

    /**
     * @param string     $name     The name
     * @param string     $header   The header
     * @param bool       $sort     Sort or not
     * @param string     $template The template
     * @param FieldAlias $alias    The alias
     */
    public function __construct($name, $header, $sort = false, $template = null, FieldAlias $alias = null)
    {
        $this->name = $name;
        $this->header = $header;
        $this->sort = $sort;
        $this->template = $template;
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return FieldAlias|null
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function hasAlias()
    {
        if (is_null($this->alias)) {
            return false;
        }

        return true;
    }

    public function getAliasObj($item)
    {
        $relation = $this->alias->getRelation();
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($accessor->isReadable($item, $relation)) {
            $item = $accessor->getValue($item, $relation);
        }

        return $item;
    }

    public function getColumnName($column)
    {
        $abbr = $this->alias->getAbbr().'.';

        if (strpos($column, $abbr) !== false) {
            $column = str_replace($abbr, '', $column);
        } else {
            throw new \Exception(" '".$abbr."' can not be found in your column name: '".$column."' ");
        }

        return $column;
    }
}
