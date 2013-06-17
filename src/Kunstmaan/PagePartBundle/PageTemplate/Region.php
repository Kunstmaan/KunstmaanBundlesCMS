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
     * @param string $name
     * @param string $span
     */
    public function __construct($name, $span)
    {
        $this->setName($name);
        $this->setSpan($span);
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setSpan($span)
    {
        $this->span = $span;
    }
}
