<?php

namespace Kunstmaan\AdminListBundle\AdminList;

/**
 */
class Field
{
    private $header;
    private $name;
    private $sort;
    private $template;

    /**
     * @param string $name
     * @param string $header
     * @param bool   $sort
     * @param string $template
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
