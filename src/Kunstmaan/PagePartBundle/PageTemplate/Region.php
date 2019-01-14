<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

/**
 * Definition of a region in a page template
 */
class Region
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var number
     */
    protected $span;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var Region[]
     */
    protected $children;

    /**
     * @var Row[]
     */
    protected $rows;

    /**
     * @param string $name
     * @param number $span
     * @param string $template
     * @param array  $children
     */
    public function __construct($name, $span, $template = null, $children = [], $rows = [])
    {
        $this->setName($name);
        $this->setSpan($span);
        $this->setTemplate($template);
        $this->setChildren($children);
        $this->setRows($rows);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Region
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return number
     */
    public function getSpan()
    {
        return $this->span;
    }

    /**
     * @param number $span
     *
     * @return Region
     */
    public function setSpan($span)
    {
        $this->span = $span;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return Region
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Region[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Region[] $children
     *
     * @return Region
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Row[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param Row[] $rows
     *
     * @return Region
     */
    public function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }
}
