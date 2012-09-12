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

    public function getName()
    {
        return $this->name;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function isSortable()
    {
        return $this->sort;
    }

    public function getTemplate()
    {
        return $this->template;
    }
}
