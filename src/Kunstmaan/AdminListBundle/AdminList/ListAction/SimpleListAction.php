<?php

namespace Kunstmaan\AdminListBundle\AdminList\ListAction;

/**
 * The simple list action is a default implementation of the list action interface, this can be used
 * in very simple use cases.
 */
class SimpleListAction implements ListActionInterface
{
    /**
     * @var array
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
     * @param array    $url      The url path and parameters
     * @param string   $label    The label
     * @param string   $icon     The icon
     * @param string   $template The template
     */
    public function __construct(array $url, $label, $icon = null, $template = null)
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->label = $label;
        $this->template = $template;
    }

    /**
     * @return array
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
