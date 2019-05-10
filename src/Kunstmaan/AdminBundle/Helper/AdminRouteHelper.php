<?php

namespace Kunstmaan\AdminBundle\Helper;

use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AdminRouteHelper
 */
class AdminRouteHelper
{
    protected static $ADMIN_MATCH_REGEX = '/^\/(app_[a-zA-Z]+\.php\/)?([a-zA-Z_-]{2,5}\/)?%s\/(.*)/';

    /**
     * @var string
     */
    protected $adminKey;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param string       $adminKey
     * @param RequestStack $requestStack
     */
    public function __construct($adminKey, RequestStack $requestStack)
    {
        $this->adminKey = $adminKey;
        $this->requestStack = $requestStack;
    }

    /**
     * Checks wether the given url points to an admin route
     *
     * @param string $url
     *
     * @return bool
     */
    public function isAdminRoute($url)
    {
        if ($this->matchesPreviewRoute($url)) {
            return false;
        }

        preg_match(sprintf(self::$ADMIN_MATCH_REGEX, $this->adminKey), $url, $matches);

        // Check if path is part of admin area
        if (count($matches) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Checks the current request if it's route is equal to SlugRouter::$SLUG_PREVIEW
     *
     * @return bool
     */
    protected function matchesPreviewRoute()
    {
        $routeName = $this->requestStack->getCurrentRequest()->get('_route');

        return $routeName === SlugRouter::$SLUG_PREVIEW;
    }
}
