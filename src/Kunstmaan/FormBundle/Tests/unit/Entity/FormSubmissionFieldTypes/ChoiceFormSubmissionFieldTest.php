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
        $this->object = new ChoiceFormSubmissionField();
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(ChoiceFormSubmissionType::class, $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
        $object = $this->object;
        $object->setChoices(['delboy1978uk' => 123456789]);
        $object->setValue('delboy1978uk');
        $string = $object->__toString();
        $this->assertEquals('123456789', $string);
        $object->setValue(['delboy1978uk', 'numkil', 'sandergo90', 'dezinc']);
        $string = $object->__toString();
        $this->assertEquals('123456789, numkil, sandergo90, dezinc', $string);
    }

    public function testIsNull()
    {
        $object = $this->object;
        $this->assertTrue($object->isNull());
        $object->setValue(array('test' => 'test'));
        $this->assertFalse($object->isNull());
        $object->setValue('blah');
        $this->assertFalse($object->isNull());
    }

    public function testSetGetValue()
    {
        $object = $this->object;
        $value = array('test' => 'test');
        $object->setValue($value);
        $this->assertEquals($value, $object->getValue());
    }

    public function testSetGetExpanded()
    {
        $object = $this->object;
        $this->assertFalse($object->getExpanded());
        $object->setExpanded(true);
        $this->assertTrue($object->getExpanded());
    }

    public function testSetGetMultiple()
    {
        $object = $this->object;
        $this->assertFalse($object->getMultiple());
        $object->setMultiple(true);
        $this->assertTrue($object->getMultiple());
    }

    public function testSetGetChoices()
    {
        $object = $this->object;
        $choices = array('test1' => 'test1', 'test2' => 'test2');
        $object->setChoices($choices);
        $this->assertEquals($choices, $object->getChoices());
    }

    public function testSetGetRequired()
    {
        $object = $this->object;
        $object->setRequired(true);
        $this->assertTrue($object->getRequired());
        $object->setRequired(false);
        $this->assertFalse($object->getRequired());
    }
}
