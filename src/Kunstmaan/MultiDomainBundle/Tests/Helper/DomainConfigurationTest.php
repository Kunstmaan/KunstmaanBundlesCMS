<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Kunstmaan\NodeBundle\Entity\Node;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DomainConfigurationTest extends TestCase
{
    /**
     * @var Node
     */
    protected $node;

    public function testGetHostWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame('multilangdomain.tld', $object->getHost());
    }

    public function testGetHostWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame('singlelangdomain.tld', $object->getHost());
    }

    public function testGetHostWithAlias()
    {
        $request = $this->getAliasedRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame('singlelangdomain.tld', $object->getHost());
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
        $this->assertSame('multilangdomain.tld', $object->getHost());
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
        $this->assertSame('singlelangdomain.tld', $object->getHost());
    }

    /**
     * @throws \ReflectionException
     */
    public function testHostOverrideReturnsNull()
    {
        $request = new Request();
        $object = $this->getDomainConfiguration($request);
        $reflection = new \ReflectionClass(DomainConfiguration::class);
        $method = $reflection->getMethod('getHostOverride');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($object));
    }

    public function testGetHosts()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame(['multilangdomain.tld', 'singlelangdomain.tld'], $object->getHosts());
    }

    public function testGetDefaultLocale()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame('en_GB', $object->getDefaultLocale());
    }

    public function testGetDefaultLocaleWithUnknownDomain()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame('en', $object->getDefaultLocale());
    }

    public function testGetExtraDataWithoutDataSet()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame([], $object->getExtraData());
    }

    public function testGetExtraDataWithDataSet()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame(['foo' => 'bar'], $object->getExtraData());
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
        $this->assertSame(['en'], $object->getFrontendLocales());
    }

    public function testGetHostBaseUrl()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame('http://multilangdomain.tld', $object->getHostBaseUrl('multilangdomain.tld'));
    }

    public function testGetHostBaseUrlWithEmptyRequestUrl()
    {
        $object = $this->getDomainConfiguration(Request::create(''));
        $this->assertNull($object->getHostBaseUrl());
    }

    public function testGetHostBaseUrlWithUnkownHost()
    {
        $object = $this->getDomainConfiguration(Request::create('unkowndomain.tld'));
        $this->assertNull($object->getHostBaseUrl());
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
        $this->assertSame(['nl', 'fr', 'en'], $object->getFrontendLocales());
    }

    public function testGetFrontendLocalesWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame(['en'], $object->getFrontendLocales());
    }

    public function testGetBackendLocalesWithSingleLanguage()
    {
        $request = $this->getSingleLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame(['en_GB'], $object->getBackendLocales());
    }

    public function testGetBackendLocalesWithMultiLanguage()
    {
        $request = $this->getMultiLanguageRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame(['nl_BE', 'fr_BE', 'en_GB'], $object->getBackendLocales());
    }

    public function testGetBackendLocalesWithUnknown()
    {
        $request = $this->getUnknownDomainRequest();
        $object = $this->getDomainConfiguration($request);
        $this->assertSame(['en'], $object->getBackendLocales());
    }

    private function getEntityManager()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em
            ->method('getRepository')
            ->with($this->equalTo(Node::class))
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

        $adminRouteHelper = $this->getMockBuilder(AdminRouteHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adminRouteHelper
            ->method('isAdminRoute')
            ->willReturnMap($adminRouteReturnValueMap);

        return $adminRouteHelper;
    }

    private function getNodeRepository()
    {
        $this->node = $this->createMock(Node::class);

        $repository = $this->getMockBuilder(NodeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('getNodeByInternalName')
            ->willReturn($this->node);

        return $repository;
    }

    private function getUnknownDomainRequest()
    {
        return Request::create('http://unknown.tld/');
    }

    private function getMultiLanguageRequest()
    {
        return Request::create('http://multilangdomain.tld/');
    }

    private function getSingleLanguageRequest()
    {
        return Request::create('http://singlelangdomain.tld/');
    }

    private function getAliasedRequest()
    {
        return Request::create('http://single-alias.tld/');
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
        $hostMap = [
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
        ];

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMainRequest')->willReturn($request);

        return new DomainConfiguration($requestStack, false, 'en', 'en', $this->getAdminRouteHelper(), $this->getEntityManager(), $hostMap);
    }
}
