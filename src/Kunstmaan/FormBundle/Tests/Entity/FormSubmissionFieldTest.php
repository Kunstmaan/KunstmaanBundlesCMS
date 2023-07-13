<?php

namespace Kunstmaan\FormBundle\Tests\Entity;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use PHPUnit\Framework\TestCase;

class Plain extends FormSubmissionField
{
}

class FormSubmissionFieldTest extends TestCase
{
    /**
     * @var StringFormSubmissionField
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new StringFormSubmissionField();
    }

    public function testSetGetId()
    {
        $object = $this->object;
        $id = 123;
        $object->setId($id);
        $this->assertSame($id, $object->getId());
    }

    public function testSetGetFieldName()
    {
        $object = $this->object;
        $fieldName = 'someFieldName';
        $object->setFieldName($fieldName);
        $this->assertSame($fieldName, $object->getFieldName());
    }

    public function testSetGetLabel()
    {
        $object = $this->object;
        $label = 'Some label';
        $object->setLabel($label);
        $this->assertSame($label, $object->getLabel());
    }

    public function testSetGetSequence()
    {
        $object = $this->object;
        $label = 'Some label';
        $object->setSequence($label);
        $this->assertSame($label, $object->getSequence());
    }

    public function testSetGetSubmission()
    {
        $object = $this->object;
        $submission = new FormSubmission();
        $submission->setId(123);
        $object->setSubmission($submission);
        $retrievedSubmission = $object->getSubmission();
        $this->assertEquals($submission, $retrievedSubmission);
        $this->assertSame($submission->getId(), $retrievedSubmission->getId());
    }

    public function testToString()
    {
        $plainObject = new Plain();
        $stringValue = $plainObject->__toString();
        $this->assertSame('FormSubmission Field', $stringValue);
    }
}
