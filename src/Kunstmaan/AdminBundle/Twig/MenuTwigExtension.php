<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanel;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MenuTwigExtension extends AbstractExtension
{
    /**
     * @var MenuBuilder
     */
    protected $menuBuilder;

    /**
     * @var AdminPanel
     */
    protected $adminPanel;

    public function __construct(MenuBuilder $menuBuilder, AdminPanel $adminPanel)
    {
        $this->menuBuilder = $menuBuilder;
        $this->adminPanel = $adminPanel;
    }

    /**
     * Get Twig functions defined in this extension.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_admin_menu', [$this, 'getAdminMenu']),
            new TwigFunction('get_admin_panel_actions', [$this, 'getAdminPanelActions']),
        ];
    }

    /**
     * Return the admin menu MenuBuilder.
     */
    public function getAdminMenu(): MenuBuilder
    {
        return $this->menuBuilder;
    }

    /**
     * Return the admin panel actions.
     *
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions(): array
    {
        return $this->adminPanel->getAdminPanelActions();
    }
}
