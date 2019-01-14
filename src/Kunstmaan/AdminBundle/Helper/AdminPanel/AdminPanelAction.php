<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

class AdminPanelAction implements AdminPanelActionInterface
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
     * @var string
     */
    private $template = 'KunstmaanAdminBundle:AdminPanel:_admin_panel_action.html.twig';

    /**
     * @param array  $url      The url path and parameters
     * @param string $label    The label
     * @param string $icon     The icon
     * @param string $template The template
     */
    public function __construct(
        array $url,
        $label,
        $icon = null,
        $template = null
    ) {
        $this->url = $url;
        $this->icon = $icon;
        $this->label = $label;
        if (!empty($template)) {
            $this->template = $template;
        }
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
