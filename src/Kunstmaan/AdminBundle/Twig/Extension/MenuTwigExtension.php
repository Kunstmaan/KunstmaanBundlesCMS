<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

class MenuTwigExtension extends \Twig_Extension
{
    /* @var MenuBuilder $menuBuilder */
    protected $menuBuilder;

    /**
     * @param MenuBuilder $menuBuilder
     */
    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'admin_menu_get'  => new \Twig_Function_Method($this, 'getAdminMenu'),
        );
    }

    /**
     * @return MenuBuilder
     */
    public function getAdminMenu()
    {
        return $this->menuBuilder;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adminmenu_twig_extension';
    }
}
