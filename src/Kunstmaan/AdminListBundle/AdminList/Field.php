<?php

namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * Field
 */
class Field
{
    private $header;
    private $name;
    private $sort;
    private $template;

    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($name, $header, $sort = false, $template = null)
    {
        $this->name     = $name;
        $this->header   = $header;
        $this->sort     = $sort;
        $this->template = $template;
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
}
