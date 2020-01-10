<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanel;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class MenuTwigExtension extends AbstractExtension
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
     * @param MenuBuilder $menuBuilder
     * @param AdminPanel  $adminPanel
     */
    public function __construct(MenuBuilder $menuBuilder, AdminPanel $adminPanel)
    {
        $this->menuBuilder = $menuBuilder;
        $this->adminPanel = $adminPanel;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('get_admin_menu', array($this, 'getAdminMenu')),
            new TwigFunction('get_admin_panel_actions', array($this, 'getAdminPanelActions')),
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
}
