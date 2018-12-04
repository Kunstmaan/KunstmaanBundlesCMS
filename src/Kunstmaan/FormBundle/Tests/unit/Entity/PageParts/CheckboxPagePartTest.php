<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;
use Kunstmaan\FormBundle\Entity\PageParts\CheckboxPagePart;
use Kunstmaan\FormBundle\Form\CheckboxPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for ChoicePagePart
 */
class CheckboxPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckboxPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CheckboxPagePart();
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

        $this->assertTrue(count($fields) == 0);
        $object->setErrorMessageRequired('omg sort it out');
        /* @var $formBuilder FormBuilderInterface */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    public function testGetDefaultAdminType()
    {
        $adminType = $this->object->getDefaultAdminType();
        $this->assertNotNull($adminType);
        $this->assertEquals(CheckboxPagePartAdminType::class, $adminType);
    }

    public function testErrorMessage()
    {
        $object = $this->object;
        $msg = 'fill in the form properly';
        $object->setErrorMessageRequired($msg);
        $this->assertEquals($msg, $object->getErrorMessageRequired());
    }
}
