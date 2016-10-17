<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;

/**
 * Tests for ChoiceFormSubmissionField
 */
class ChoiceFormSubmissionFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChoiceFormSubmissionField
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ChoiceFormSubmissionField;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertEquals(ChoiceFormSubmissionType::class, $this->object->getDefaultAdminType());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::__toString
     */
    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::isNull
     */
    public function testIsNull()
    {
        $object = $this->object;
        $this->assertTrue($object->isNull());
        $object->setValue(array('test' => 'test'));
        $this->assertFalse($object->isNull());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::getValue
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::setValue
     */
    public function testSetGetValue()
    {
        $object = $this->object;
        $value = array('test' => 'test');
        $object->setValue($value);
        $this->assertEquals($value, $object->getValue());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::setExpanded
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::getExpanded
     */
    public function testSetGetExpanded()
    {
        $object = $this->object;
        $this->assertFalse($object->getExpanded());
        $object->setExpanded(true);
        $this->assertTrue($object->getExpanded());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::setMultiple
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::getMultiple
     */
    public function testSetGetMultiple()
    {
        $object = $this->object;
        $this->assertFalse($object->getMultiple());
        $object->setMultiple(true);
        $this->assertTrue($object->getMultiple());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::setChoices
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField::getChoices
     */
    public function testSetGetChoices()
    {
        $object = $this->object;
        $choices = array('test1' => 'test1', 'test2' => 'test2');
        $object->setChoices($choices);
        $this->assertEquals($choices, $object->getChoices());
    }

}
