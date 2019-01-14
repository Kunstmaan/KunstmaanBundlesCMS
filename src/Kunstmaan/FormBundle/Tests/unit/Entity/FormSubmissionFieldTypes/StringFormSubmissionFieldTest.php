<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;

/**
 * Tests for StringFormSubmissionField
 */
class StringFormSubmissionFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringFormSubmissionField
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
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
        $this->assertTrue(is_string($stringValue));
    }
}
