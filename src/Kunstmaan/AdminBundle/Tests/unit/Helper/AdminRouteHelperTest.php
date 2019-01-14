<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminRouteHelperTest extends \PHPUnit_Framework_TestCase
{
    protected static $ADMIN_KEY = 'admin';

    protected static $ALTERNATIVE_ADMIN_KEY = 'vip';

    protected static $NON_ADMIN_URL = '/en/some_path/%s/nodes';

    protected static $ADMIN_URL = '/en/%s/nodes';

    protected static $PREVIEW_ADMIN_URL = '/en/%s/preview/blog/page/1';

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\AdminRouteHelper::isAdminRoute
     */
    public function testIsAdminRouteReturnsTrueWhenAdminUrl()
    {
        $adminRouteHelper = $this->getAdminRouteHelper(self::$ADMIN_KEY);
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$ADMIN_URL, self::$ADMIN_KEY));
        $this->assertTrue($result);

        $adminRouteHelper = $this->getAdminRouteHelper(self::$ALTERNATIVE_ADMIN_KEY);
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$ADMIN_URL, self::$ALTERNATIVE_ADMIN_KEY));
        $this->assertTrue($result);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\AdminRouteHelper::isAdminRoute
     */
    public function testIsAdminRouteReturnsFalseWhenFrontendUrl()
    {
        $adminRouteHelper = $this->getAdminRouteHelper(self::$ADMIN_KEY);
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$NON_ADMIN_URL, self::$ADMIN_KEY));
        $this->assertFalse($result);

        $adminRouteHelper = $this->getAdminRouteHelper(self::$ALTERNATIVE_ADMIN_KEY);
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$NON_ADMIN_URL, self::$ALTERNATIVE_ADMIN_KEY));
        $this->assertFalse($result);
    }

    /**
     * @covers \Kunstmaan\AdminBundle\Helper\AdminRouteHelper::isAdminRoute
     */
    public function testIsAdminRouteReturnsFalseWhenPreviewUrl()
    {
        $requestStack = new RequestStack();
        $requestStack->push($this->getPreviewRequest());

        $adminRouteHelper = new AdminRouteHelper(self::$ADMIN_KEY, $requestStack);
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$PREVIEW_ADMIN_URL, self::$ADMIN_KEY));
        $this->assertFalse($result);

        $adminRouteHelper = new AdminRouteHelper(self::$ALTERNATIVE_ADMIN_KEY, $requestStack);
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$PREVIEW_ADMIN_URL, self::$ALTERNATIVE_ADMIN_KEY));
        $this->assertFalse($result);
    }

    private function getAdminRouteHelper($adminKey)
    {
        return new AdminRouteHelper($adminKey, $this->getRequestStack());
    }

    private function getRequestStack()
    {
        $requestStack = new RequestStack();
        $requestStack->push($this->getRequest());

        return $requestStack;
    }

    private function getRequest()
    {
        return Request::create('http://domain.tld/');
    }

    private function getPreviewRequest()
    {
        return Request::create('http://domain.tld/', 'GET', array('_route' => SlugRouter::$SLUG_PREVIEW));
    }
}
