<?php

namespace Kunstmaan\LanguageChooserBundle\Tests\LocaleGuesser;

use Kunstmaan\LanguageChooserBundle\LocaleGuesser\UrlLocaleGuesser;
use Lunetics\LocaleBundle\Validator\MetaValidator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * UrlLocaleGuesserTest
 */
class UrlLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetaValidator
     */
    protected $metaValidator;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlLocaleGuesser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->metaValidator = $this->getMockBuilder('Lunetics\LocaleBundle\Validator\MetaValidator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request->attributes = new ParameterBag();

        $this->object = new UrlLocaleGuesser($this->metaValidator);
    }

    /**
     * @covers Kunstmaan\LanguageChooserBundle\LocaleGuesser\UrlLocaleGuesser::guessLocale
     */
    public function testFoundLocalePathAttribute()
    {
        $this->metaValidator->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $this->request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/de/some-path'));

        $this->request->attributes->set('path', '/de/some-path');

        $this->assertTrue($this->object->guessLocale($this->request));
        $this->assertEquals('de', $this->object->getIdentifiedLocale());
    }

    /**
     * @covers Kunstmaan\LanguageChooserBundle\LocaleGuesser\UrlLocaleGuesser::guessLocale
     */
    public function testFoundLocalePathInfo()
    {
        $this->metaValidator->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $this->request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/fr/some-other-path'));

        $this->assertTrue($this->object->guessLocale($this->request));
        $this->assertEquals('fr', $this->object->getIdentifiedLocale());
    }

    /**
     * @covers Kunstmaan\LanguageChooserBundle\LocaleGuesser\UrlLocaleGuesser::guessLocale
     */
    public function testNullPath()
    {
        $this->metaValidator->expects($this->never())
            ->method('isAllowed');

        $this->request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue(null));

        $this->assertFalse($this->object->guessLocale($this->request));
        $this->assertEquals(null, $this->object->getIdentifiedLocale());
    }

    /**
     * @covers Kunstmaan\LanguageChooserBundle\LocaleGuesser\UrlLocaleGuesser::guessLocale
     */
    public function testInvalidLocaleFound()
    {
        $this->metaValidator->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/en/another-path'));

        $this->assertFalse($this->object->guessLocale($this->request));
        $this->assertEquals(null, $this->object->getIdentifiedLocale());
    }
}
