<?php

namespace Kunstmaan\AdminBundle\Twig;

use Twig_Environment;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\Form\FormView;

class AdminRouteHelperTwigExtension extends \Twig_Extension
{
    /** @var AdminRouteHelper $adminRouteHelper */
    private $adminRouteHelper;

    /**
     * @param AdminRouteHelper $adminRouteHelper
     */
    public function __construct(AdminRouteHelper $adminRouteHelper)
    {
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_admin_route', array($this, 'isAdminRoute')),
        );
    }

    /**
     * Lets the adminroutehelper determine wether the URI is an admin route
     *
     * @return boolean
     */
    public function isAdminRoute($URI)
    {
        return $this->adminRouteHelper->isAdminRoute($URI);
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'admin_route_helper_twig_extension';
    }
}
