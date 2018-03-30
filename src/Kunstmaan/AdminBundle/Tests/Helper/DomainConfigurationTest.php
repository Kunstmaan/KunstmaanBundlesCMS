<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DomainConfigurationTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals(array('domain.tld'), $object->getHosts());
    }

    public function testGetDefaultLocale()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals('en', $object->getDefaultLocale());
    }

    public function testGetExtraData()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(array(), $object->getExtraData());
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
        $this->assertEquals(array('en'), $object->getFrontendLocales());
    }

    public function testGetFrontendLocalesWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertEquals(array('nl', 'fr', 'en'), $object->getFrontendLocales());
    }

    public function testGetBackendLocalesWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(array('en'), $object->getBackendLocales());
    }

    public function testGetBackendLocalesWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertEquals(array('nl', 'fr', 'en'), $object->getBackendLocales());
    }

    private function getContainer($map)
    {
        $this->container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->container
            ->method('getParameter')
            ->will($this->returnValueMap($map));
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('request_stack'))
            ->willReturn($this->getRequestStack());

        return $this->container;
    }

    private function getRequestStack()
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn($this->getRequest());

        return $requestStack;
    }

    private function getRequest()
    {
        $request = Request::create('http://domain.tld/');

        return $request;
    }

    private function getSingleLanguageDomainConfiguration()
    {
        $map = array(
            array('multilanguage', false),
            array('defaultlocale', 'en'),
            array('requiredlocales', 'en'),
        );

        $object = new DomainConfiguration($this->getContainer($map));

        return $object;
    }

    private function getMultiLanguageDomainConfiguration()
    {
        $map = array(
            array('multilanguage', true),
            array('defaultlocale', 'nl'),
            array('requiredlocales', 'nl|fr|en'),
        );

        $object = new DomainConfiguration($this->getContainer($map));

        return $object;
    }
}
