<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Kunstmaan\NodeBundle\Entity\Node;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DomainConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Node
     */
    protected $node;

    public function testGetHostWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('multilangdomain.tld', $object->getHost());
    }

    public function testGetHostWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('singlelangdomain.tld', $object->getHost());
    }

    public function testGetHostWithAlias()
    {
        $request = $this->getAliasedRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('singlelangdomain.tld', $object->getHost());
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::__construct
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::getMasterRequest
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::getHost
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::hasHostOverride
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::getHostOverride
     */
    public function testGetHostWithOverrideOnFrontend()
    {
        $request = $this->getRequestWithOverride('/frontend-uri');
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('multilangdomain.tld', $object->getHost());
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::__construct
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::getMasterRequest
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::getHost
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::hasHostOverride
     * @covers \Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration::getHostOverride
     */
    public function testGetHostWithOverrideOnBackend()
    {
        $request = $this->getRequestWithOverride('/nl/admin/backend-uri');
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('singlelangdomain.tld', $object->getHost());
    }

    /**
     * @throws \ReflectionException
     */
    public function testHostOverrideReturnsNull()
    {
        $request = new Request();
        $object = $this->getDomainConfiguration($request);
        $reflection = new ReflectionClass(DomainConfiguration::class);
        $method = $reflection->getMethod('getHostOverride');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($object));
    }

    public function testGetHosts()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('multilangdomain.tld', 'singlelangdomain.tld'), $object->getHosts());
    }

    public function testGetDefaultLocale()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('en_GB', $object->getDefaultLocale());
    }

    public function testGetDefaultLocaleWithUnknownDomain()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('en', $object->getDefaultLocale());
    }

    public function testGetExtraDataWithoutDataSet()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array(), $object->getExtraData());
    }

    public function testGetExtraDataWithDataSet()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('foo' => 'bar'), $object->getExtraData());
    }

    public function testGetRootNode()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals($this->node, $object->getRootNode());
    }

    public function testGetRootNodeWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertNull($object->getRootNode());
    }

    public function testIsMultiDomainHostWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertTrue($object->isMultiDomainHost());
    }

    public function testIsMultiDomainHostWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertTrue($object->isMultiDomainHost());
    }

    public function testIsMultiDomainHostWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertFalse($object->isMultiDomainHost());
    }

    public function testIsMultiLanguageWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertFalse($object->isMultiLanguage());
    }

    public function testIsMultiLanguageWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertTrue($object->isMultiLanguage());
    }

    public function testIsMultiLanguageWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertFalse($object->isMultiLanguage());
    }

    public function testGetFrontendLocalesWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('en'), $object->getFrontendLocales());
    }

    public function testGetHostBaseUrl()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals('http://multilangdomain.tld', $object->getHostBaseUrl('multilangdomain.tld'));
    }

    public function testGetFullHost()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertNull($object->getFullHost('not-here.tld'));
    }

    public function testGetLocalesExtraData()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $data = $object->getLocalesExtraData();
        $this->assertEmpty($data);
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $data = $object->getLocalesExtraData();
        $this->assertArrayHasKey('foo', $data);
    }

    public function testGetFullHostConfig()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $array = $object->getFullHostConfig();
        $this->assertArrayHasKey('multilangdomain.tld', $array);
        $this->assertArrayHasKey('singlelangdomain.tld', $array);
    }

    public function testHasHostSwitched()
    {
        $request = $this->getRequestWithOverride('/admin/somewhere');
        $object = $this->getDomainConfiguration($request);
        $this->assertTrue($object->hasHostSwitched());
    }

    public function testGetHostSwitched()
    {
        $request = $this->getRequestWithOverride('/admin/somewhere');
        $object = $this->getDomainConfiguration($request);
        $switched = $object->getHostSwitched();
        $this->assertArrayHasKey('id', $switched);
        $this->assertArrayHasKey('host', $switched);
        $this->assertArrayHasKey('protocol', $switched);
        $this->assertArrayHasKey('type', $switched);
    }

    public function testFullHostById()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertNull($object->getFullHostById(666));
        $this->assertNotNull($object->getFullHostById(123));
    }

    public function testGetFrontendLocalesWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('nl', 'fr', 'en'), $object->getFrontendLocales());
    }

    public function testGetFrontendLocalesWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('en'), $object->getFrontendLocales());
    }

    public function testGetBackendLocalesWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('en_GB'), $object->getBackendLocales());
    }

    public function testGetBackendLocalesWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('nl_BE', 'fr_BE', 'en_GB'), $object->getBackendLocales());
    }

    public function testGetBackendLocalesWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertEquals(array('en'), $object->getBackendLocales());
    }

    private function getContainer($map, $request)
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container
            ->method('getParameter')
            ->will($this->returnValueMap($map));

        $serviceMap = array(
            array('request_stack', 1, $this->getRequestStack($request)),
            array('doctrine.orm.entity_manager', 1, $this->getEntityManager()),
            array('kunstmaan_admin.adminroute.helper', 1, $this->getAdminRouteHelper()),
        );

        $container
            ->method('get')
            ->will($this->returnValueMap($serviceMap));

        return $container;
    }

    private function getEntityManager()
    {
        $em = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $em
            ->method('getRepository')
            ->with($this->equalTo('KunstmaanNodeBundle:Node'))
            ->willReturn($this->getNodeRepository());

        return $em;
    }

    private function getAdminRouteHelper()
    {
        $adminRouteReturnValueMap = [
            ['/frontend-uri', false],
            ['/nl/admin/backend-uri', true],
            ['/admin/somewhere', true],
        ];

        $adminRouteHelper = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\AdminRouteHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $adminRouteHelper
            ->expects($this->any())
            ->method('isAdminRoute')
            ->will($this->returnValueMap($adminRouteReturnValueMap));

        return $adminRouteHelper;
    }

    private function getNodeRepository()
    {
        $repository = $this->getMockBuilder('Kunstmaan\NodeBundle\Repository\NodeRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('getNodeByInternalName')
            ->willReturn($this->getRootNode());

        return $repository;
    }

    private function getRootNode()
    {
        $this->node = $this->createMock('Kunstmaan\NodeBundle\Entity\Node');

        return $this->node;
    }

    private function getRequestStack($request)
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn($request);

        return $requestStack;
    }

    private function getUnknownDomainRequest()
    {
        $request = Request::create('http://unknown.tld/');

        return $request;
    }

    private function getMultiLanguageRequest()
    {
        $request = Request::create('http://multilangdomain.tld/');

        return $request;
    }

    private function getSingleLanguageRequest()
    {
        $request = Request::create('http://singlelangdomain.tld/');

        return $request;
    }

    private function getAliasedRequest()
    {
        $request = Request::create('http://single-alias.tld/');

        return $request;
    }

    private function getRequestWithOverride($uri)
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set(DomainConfiguration::OVERRIDE_HOST, 'singlelangdomain.tld');
        $session->set(DomainConfiguration::SWITCH_HOST, 'multilangdomain.tld');

        $request = Request::create('http://multilangdomain.tld' . $uri);
        $request->setSession($session);
        $request->cookies->set($session->getName(), null);

        return $request;
    }

    private function getDomainConfiguration($request)
    {
        $hostMap = array(
            'multilangdomain.tld' => [
                'id' => 456,
                'host' => 'multilangdomain.tld',
                'protocol' => 'http',
                'type' => 'multi_lang',
                'default_locale' => 'en_GB',
                'locales' => ['nl' => 'nl_BE', 'fr' => 'fr_BE', 'en' => 'en_GB'],
                'reverse_locales' => ['nl_BE' => 'nl', 'fr_BE' => 'fr', 'en_GB' => 'en'],
                'root' => 'homepage_multi',
                'aliases' => ['multi-alias.tld'],
                'extra' => ['foo' => 'bar'],
                'locales_extra' => ['foo' => 'bar'],
            ],
            'singlelangdomain.tld' => [
                'id' => 123,
                'host' => 'singlelangdomain.tld',
                'type' => 'single_lang',
                'default_locale' => 'en_GB',
                'locales' => ['en' => 'en_GB'],
                'reverse_locales' => ['en_GB' => 'en'],
                'root' => 'homepage_single',
                'aliases' => ['single-alias.tld'],
            ],
        );

        $map = array(
            array('multilanguage', false),
            array('defaultlocale', 'en'),
            array('requiredlocales', 'en'),
            array('kunstmaan_multi_domain.hosts', $hostMap),
        );

        $object = new DomainConfiguration($this->getContainer($map, $request));

        return $object;
    }
}
