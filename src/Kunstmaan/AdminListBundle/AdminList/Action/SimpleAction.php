<?php

namespace Kunstmaan\AdminListBundle\AdminList\Action;

/**
 * SimpleAction
 */
class SimpleAction implements ActionInterface
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $label;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @param string $url      The url
     * @param string $icon     The icon
     * @param string $label    The label
     * @param string $template The template
     */
    public function __construct($url, $icon, $label, $template = null)
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->label = $label;
        $this->template = $template;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getUrlFor($item)
    {
        return $this->url;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return $this->icon;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

}
