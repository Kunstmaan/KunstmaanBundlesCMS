<?php

namespace Kunstmaan\AdminListBundle\AdminList;

class SimpleAction implements ListActionInterface
{
    private $url;
    private $icon;
    private $label;
    private $template;

    public function __construct($url, $icon, $label, $template = null)
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->label = $label;
        $this->template = $template;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getTemplate()
    {
        return $this->template;
    }

}
