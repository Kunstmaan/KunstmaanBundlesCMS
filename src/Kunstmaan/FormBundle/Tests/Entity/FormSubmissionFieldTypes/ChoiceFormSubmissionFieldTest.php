<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use PHPUnit\Framework\TestCase;

/**
 * Tests for ChoiceFormSubmissionField
 */
class ChoiceFormSubmissionFieldTest extends TestCase
{
    /**
     * @var ChoiceFormSubmissionField
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ChoiceFormSubmissionField();
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(ChoiceFormSubmissionType::class, $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
        $object = $this->object;
        $object->setChoices(['delboy1978uk' => 123_456_789]);
        $object->setValue('delboy1978uk');
        $string = $object->__toString();
        $this->assertSame('123456789', $string);
        $object->setValue(['delboy1978uk', 'numkil', 'sandergo90', 'dezinc']);
        $string = $object->__toString();
        $this->assertSame('123456789, numkil, sandergo90, dezinc', $string);
    }

    public function testIsNull()
    {
        $object = $this->object;
        $this->assertTrue($object->isNull());
        $object->setValue(['test' => 'test']);
        $this->assertFalse($object->isNull());
        $object->setValue('blah');
        $this->assertFalse($object->isNull());
    }

    public function testSetGetValue()
    {
        $object = $this->object;
        $value = ['test' => 'test'];
        $object->setValue($value);
        $this->assertSame($value, $object->getValue());
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
        $choices = ['test1' => 'test1', 'test2' => 'test2'];
        $object->setChoices($choices);
        $this->assertSame($choices, $object->getChoices());
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
