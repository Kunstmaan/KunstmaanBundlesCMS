<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;

use Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart;
use Kunstmaan\FormBundle\Form\EmailPagePartAdminType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for EmailPagePart
 */
class EmailPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new EmailPagePart;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::setErrorMessageRequired
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::getErrorMessageRequired
     */
    public function testSetErrorMessageRequired()
    {
        $object = $this->object;
        $object->setErrorMessageRequired('');
        $this->assertEquals('', $object->getErrorMessageRequired());

        $message = "Some example required message";
        $object->setErrorMessageRequired($message);
        $this->assertEquals($message, $object->getErrorMessageRequired());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::setErrorMessageInvalid
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::getErrorMessageInvalid
     */
    public function testSetErrorMessageInvalid()
    {
        $object = $this->object;
        $object->setErrorMessageInvalid('');
        $this->assertEquals('', $object->getErrorMessageInvalid());

        $message = "Some example invalid message";
        $object->setErrorMessageInvalid($message);
        $this->assertEquals($message, $object->getErrorMessageInvalid());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::getDefaultView
     */

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::adaptForm
     */
    public function testAdaptForm()
    {
        $object = $this->object;
        $object->setRequired(true);

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(array()));

        $fields = new ArrayObject();

        $this->assertTrue(sizeof($fields) == 0);
        /* @var $formBuilder FormBuilderInterface */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(sizeof($fields) > 0);
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertEquals(EmailPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
