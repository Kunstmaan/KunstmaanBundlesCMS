<?php

namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * FieldAlias
 */
class FieldAlias
{
    /**
     * FieldAlias constructor.
     *
     * @param $abbr string
     * @param $relation string
     */
    public function __construct($abbr, $relation)
    {
        $this->abbr = $abbr;
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }
}
