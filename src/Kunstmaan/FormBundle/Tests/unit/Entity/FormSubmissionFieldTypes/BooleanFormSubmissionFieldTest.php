<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\FormBundle\Form\BooleanFormSubmissionType;

/**
 * Tests for StringFormSubmissionField
 */
class BooleanFormSubmissionFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetValue()
    {
        $object = new BooleanFormSubmissionField();
        $object->setValue(true);
        $this->assertTrue($object->getValue());
        $this->assertEquals('true', $object->__toString());
        $this->assertEquals(BooleanFormSubmissionType::class, $object->getDefaultAdminType());
        $object->setValue(false);
        $this->assertFalse($object->getValue());
        $this->assertEquals('false', $object->__toString());
    }
}
