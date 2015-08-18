<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanel;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

/**
 * MenuTwigExtension
 */
class MenuTwigExtension extends \Twig_Extension
{
    /**
     * @var MenuBuilder
     */
    protected $menuBuilder;

    /**
     * @var AdminPanel
     */
    protected $adminPanel;

    /**
     * Constructor
     *
     * @param MenuBuilder $menuBuilder
     */
    public function __construct(
        MenuBuilder $menuBuilder,
        AdminPanel $adminPanel
    ) {
        $this->menuBuilder = $menuBuilder;
        $this->adminPanel  = $adminPanel;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'get_admin_menu' => new \Twig_Function_Method(
                $this, 'getAdminMenu'
            ),
            'get_admin_panel_actions' => new \Twig_Function_Method(
                $this, 'getAdminPanelActions'
            ),
        );
    }

    /**
     * Return the admin menu MenuBuilder.
     *
     * @return MenuBuilder
     */
    public function getAdminMenu()
    {
        return $this->menuBuilder;
    }

    /**
     * Return the admin panel actions.
     *
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return $this->adminPanel->getAdminPanelActions();
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'adminmenu_twig_extension';
    }
}
