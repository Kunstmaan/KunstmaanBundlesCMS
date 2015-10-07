<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DomainConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainConfiguration
     */
    protected $object;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::__construct
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getMasterRequest
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getHost
     */
    public function testGetHost()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals('domain.tld', $object->getHost());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getHosts
     */
    public function testGetHosts()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(array('domain.tld'), $object->getHosts());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getDefaultLocale
     */
    public function testGetDefaultLocale()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals('en', $object->getDefaultLocale());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getExtraData
     */
    public function testGetExtraData()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(array(), $object->getExtraData());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getRootNode
     */
    public function testGetRootNode()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertNull($object->getRootNode());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::isMultiDomainHost
     */
    public function testIsMultiDomainHost()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertFalse($object->isMultiDomainHost());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::isMultiLanguage
     */
    public function testIsMultiLanguageWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertFalse($object->isMultiLanguage());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::isMultiLanguage
     */
    public function testIsMultiLanguageWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertTrue($object->isMultiLanguage());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getFrontendLocales
     */
    public function testGetFrontendLocalesWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(array('en'), $object->getFrontendLocales());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getFrontendLocales
     */
    public function testGetFrontendLocalesWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertEquals(array('nl', 'fr', 'en'), $object->getFrontendLocales());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getBackendLocales
     */
    public function testGetBackendLocalesWithSingleLanguage()
    {
        $object = $this->getSingleLanguageDomainConfiguration();
        $this->assertEquals(array('en'), $object->getBackendLocales());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\DomainConfiguration::getBackendLocales
     */
    public function testGetBackendLocalesWithMultiLanguage()
    {
        $object = $this->getMultiLanguageDomainConfiguration();
        $this->assertEquals(array('nl', 'fr', 'en'), $object->getBackendLocales());
    }

    private function getContainer($map)
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

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
        $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
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
