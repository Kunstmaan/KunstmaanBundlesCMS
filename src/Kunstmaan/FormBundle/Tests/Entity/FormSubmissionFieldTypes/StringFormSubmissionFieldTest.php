<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use PHPUnit\Framework\TestCase;

/**
 * Tests for StringFormSubmissionField
 */
class StringFormSubmissionFieldTest extends TestCase
{
    /**
     * @var StringFormSubmissionField
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new StringFormSubmissionField();
    }

    public function testSetGetValue()
    {
        $object = $this->object;
        $value = 'test';
        $object->setValue($value);
        $this->assertEquals($value, $object->getValue());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(StringFormSubmissionType::class, $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }
}
