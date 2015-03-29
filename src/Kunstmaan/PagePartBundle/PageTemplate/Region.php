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
     * @param string $name
     * @param number $span
     * @param string $template
     */
    public function __construct($name, $span, $template = null)
    {
        $this->setName($name);
        $this->setSpan($span);
	$this->setTemplate($template);
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
}
