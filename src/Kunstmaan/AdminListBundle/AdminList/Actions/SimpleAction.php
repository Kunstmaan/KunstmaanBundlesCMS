<?php

namespace Kunstmaan\AdminListBundle\AdminList\Actions;

use Kunstmaan\AdminListBundle\AdminList\ListActionInterface;

/**
 * SimpleAction
 */
class SimpleAction implements ListActionInterface
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
