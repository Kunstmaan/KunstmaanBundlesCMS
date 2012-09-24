<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

/**
 * MenuTwigExtension
 */
class MenuTwigExtension extends \Twig_Extension
{
    /**
     * @var MenuBuilder $menuBuilder
     */
    protected $menuBuilder;

    /**
     * Constructor
     *
     * @param MenuBuilder $menuBuilder
     */
    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'admin_menu_get'  => new \Twig_Function_Method($this, 'getAdminMenu'),
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
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'adminmenu_twig_extension';
    }
}
