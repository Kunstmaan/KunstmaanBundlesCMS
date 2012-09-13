<?php

namespace Kunstmaan\AdminListBundle\AdminList;

class SimpleAction implements ListActionInterface
{
    private $url;
    private $icon;
    private $label;
    private $template;

    /**
     * @param string $url
     * @param string $icon
     * @param string $label
     * @param string $template
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
