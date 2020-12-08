<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class AdminRouteHelperTwigExtension extends AbstractExtension
{
    /** @var AdminRouteHelper */
    private $adminRouteHelper;

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
        return [
            new TwigFunction('is_admin_route', [$this, 'isAdminRoute']),
        ];
    }

    /**
     * Lets the adminroutehelper determine wether the URI is an admin route
     *
     * @return bool
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
