<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminRouteHelperTest extends TestCase
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

    /**
     * @dataProvider encodedUrlProvider
     */
    public function testIsAdminRouteMatchesTheDecodedPath(string $url, bool $expected)
    {
        $adminRouteHelper = $this->getAdminRouteHelper(self::$ADMIN_KEY);

        $this->assertSame($expected, $adminRouteHelper->isAdminRoute($url));
    }

    public function encodedUrlProvider(): iterable
    {
        yield 'encoded admin key' => ['/%61dmin/nodes', true];
        yield 'fully encoded admin key' => ['/%61%64%6d%69%6e/nodes', true];
        yield 'encoded admin key with locale' => ['/en/%61dmin/nodes', true];
        yield 'encoded admin key with front controller' => ['/app_dev.php/%61dmin/nodes', true];
        yield 'encoded slash' => ['/en%2Fadmin/nodes', true];
        yield 'query string' => ['/admin/nodes?foo=bar', true];
        yield 'admin key only in query string' => ['/en/some_path?redirect=/admin/nodes', false];
        yield 'double encoded admin key' => ['/%2561dmin/nodes', false];
        yield 'encoded frontend url' => ['/en/%73ome_path/admin/nodes', false];
    }

    public function testIsAdminRouteWorksWhenNoRequestInRequeststack()
    {
        $adminRouteHelper = new AdminRouteHelper(self::$ADMIN_KEY, new RequestStack());
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$PREVIEW_ADMIN_URL, self::$ADMIN_KEY));
        $this->assertTrue($result);

        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$NON_ADMIN_URL, self::$ALTERNATIVE_ADMIN_KEY));
        $this->assertFalse($result);

        $adminRouteHelper = new AdminRouteHelper(self::$ALTERNATIVE_ADMIN_KEY, new RequestStack());
        $result = $adminRouteHelper->isAdminRoute(sprintf(self::$PREVIEW_ADMIN_URL, self::$ALTERNATIVE_ADMIN_KEY));
        $this->assertTrue($result);
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
        $request = Request::create('http://domain.tld/');
        $request->attributes->set('_route', SlugRouter::$SLUG_PREVIEW);

        return $request;
    }
}
