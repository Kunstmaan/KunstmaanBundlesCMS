<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField;
use Kunstmaan\FormBundle\Form\EmailFormSubmissionType;
use PHPUnit\Framework\TestCase;

/**
 * Tests for EmailFormSubmissionField
 */
class EmailFormSubmissionFieldTest extends TestCase
{
    /**
     * @var EmailFormSubmissionField
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new EmailFormSubmissionField();
    }

    public function testSetGetValue()
    {
        $object = $this->object;
        $value = 'test@test.be';
        $object->setValue($value);
        $this->assertEquals($value, $object->getValue());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(EmailFormSubmissionType::class, $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }
}
