<?php

namespace Kunstmaan\FormBundle\Tests\Tests\Entity\PageParts;

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
        $this->object = new EmailPagePart();
    }

    public function testSetErrorMessageRequired()
    {
        $object = $this->object;
        $object->setErrorMessageRequired('');
        $this->assertEquals('', $object->getErrorMessageRequired());

        $message = 'Some example required message';
        $object->setErrorMessageRequired($message);
        $this->assertEquals($message, $object->getErrorMessageRequired());
    }

    public function testSetErrorMessageInvalid()
    {
        $object = $this->object;
        $object->setErrorMessageInvalid('');
        $this->assertEquals('', $object->getErrorMessageInvalid());

        $message = 'Some example invalid message';
        $object->setErrorMessageInvalid($message);
        $this->assertEquals($message, $object->getErrorMessageInvalid());
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

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

        $object->setErrorMessageRequired('form error!');
        $object->setErrorMessageInvalid('not valid');
        $this->assertTrue(count($fields) == 0);
        /* @var $formBuilder FormBuilderInterface */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(EmailPagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testRequired()
    {
        $object = $this->object;

        $object->setRequired(true);
        $this->assertTrue($object->getRequired());
    }
}
