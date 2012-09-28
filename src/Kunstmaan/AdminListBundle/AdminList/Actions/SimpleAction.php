<?php

namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * SimpleAction
 */
class SimpleAction implements ListActionInterface
{
    private $url;
    private $icon;
    private $label;
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLabel()
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
