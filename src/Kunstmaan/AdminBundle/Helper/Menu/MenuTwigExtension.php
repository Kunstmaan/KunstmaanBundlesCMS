<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;


class MenuTwigExtension extends \Twig_Extension
{
    protected $em;
    protected $menubuilder;

    public function __construct($em, $menubuilder)
    {
        $this->em = $em;
        $this->menubuilder = $menubuilder;
    }

    public function getFunctions() {
        return array(
        	'admin_menu_get'  => new \Twig_Function_Method($this, 'getAdminMenu'),
        );
    }

    public function getAdminMenu()
    {
        return $this->menubuilder;
    }

    public function getName()
    {
        return 'adminmenu_twig_extension';
    }
}
