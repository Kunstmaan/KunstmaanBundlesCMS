<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainConfigurationTest extends TestCase
{
    /**
     * @var DomainConfiguration
     */
    protected $object;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function testGetHost()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals('domain.tld', $object->getHost());
    }

    public function testGetHosts()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(['domain.tld'], $object->getHosts());
    }

    public function testGetDefaultLocale()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals('en', $object->getDefaultLocale());
    }

    public function testGetExtraData()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals([], $object->getExtraData());
    }

    public function testGetRootNode()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertNull($object->getRootNode());
    }

    public function testIsMultiDomainHost()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertFalse($object->isMultiDomainHost());
    }

    public function testIsMultiLanguageWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertFalse($object->isMultiLanguage());
    }

    public function testIsMultiLanguageWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertTrue($object->isMultiLanguage());
    }

    public function testGetFrontendLocalesWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(['en'], $object->getFrontendLocales());
    }

    public function testGetFrontendLocalesWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertEquals(['nl', 'fr', 'en'], $object->getFrontendLocales());
    }

    public function testGetBackendLocalesWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(['en'], $object->getBackendLocales());
    }

    public function testGetBackendLocalesWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertEquals(['nl', 'fr', 'en'], $object->getBackendLocales());
    }

    private function getRequestStack()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn(Request::create('http://domain.tld/'));

        return $requestStack;
    }

    private function getSingleLanguageDomainConfiguration()
    {
        return new DomainConfiguration($this->getRequestStack(), false, 'en', 'en');
    }

    private function getMultiLanguageDomainConfiguration()
    {
        return new DomainConfiguration($this->getRequestStack(), true, 'nl', 'nl|fr|en');
    }
}
