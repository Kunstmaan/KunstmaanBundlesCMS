<?php
namespace Kunstmaan\AdminListBundle\AdminList;
class SimpleAction implements ActionInterface
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

    public function getUrlFor($item)
    {
        return $this->url;
    }

    public function getIcon($item)
    {
        return $this->icon;
    }

    public function getLabel($item)
    {
        return $this->label;
    }

    public function getTemplate()
    {
        return $this->template;
    }

}
