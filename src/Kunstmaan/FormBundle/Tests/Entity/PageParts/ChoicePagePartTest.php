<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;

use Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for ChoicePagePart
 */
class ChoicePagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChoicePagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ChoicePagePart;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::getDefaultView
     */
    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::adaptForm
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

        $this->assertTrue(count($fields) == 0);
        /* @var $formBuilder FormBuilderInterface */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertEquals(ChoicePagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::setExpanded
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::getExpanded
     */
    public function testSetGetExpanded()
    {
        $object = $this->object;
        $this->assertFalse($object->getExpanded());
        $object->setExpanded(true);
        $this->assertTrue($object->getExpanded());
    }


    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::setMultiple
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::getMultiple
     */
    public function testSetGetMultiple()
    {
        $object = $this->object;
        $this->assertFalse($object->getMultiple());
        $object->setMultiple(true);
        $this->assertTrue($object->getMultiple());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::setChoices
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::getChoices
     */
    public function testSetGetChoices()
    {
        $object = $this->object;
        $choices = array('test1' => 'test1', 'test2' => 'test2');
        $object->setChoices($choices);
        $this->assertEquals($choices, $object->getChoices());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::setEmptyValue
     * @covers Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart::getEmptyValue
     */
    public function testSetGetEmptyValue()
    {
        $object = $this->object;
        $value = 'test';
        $object->setEmptyValue($value);
        $this->assertEquals($value, $object->getEmptyValue());
    }

}
