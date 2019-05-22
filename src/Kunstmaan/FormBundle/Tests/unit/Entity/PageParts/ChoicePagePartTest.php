<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;
use Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for ChoicePagePart
 */
class ChoicePagePartTest extends TestCase
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
        $this->object = new ChoicePagePart();
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertInternalType('string', $stringValue);
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
            ->willReturn([]);

        $fields = new ArrayObject();

        $this->assertEquals(count($fields), 0);
        $object->setErrorMessageRequired('invalid!');
        /* @var FormBuilderInterface $formBuilder */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(ChoicePagePartAdminType::class, $this->object->getDefaultAdminType());
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

    public function testGettersAndSetters()
    {
        $object = $this->object;
        $value = 'test';
        $object->setEmptyValue($value);
        $object->setRequired(true);
        $object->setErrorMessageRequired('fix your code!');

        $this->assertEquals($value, $object->getEmptyValue());
        $this->assertTrue($object->getRequired());
        $this->assertEquals('fix your code!', $object->getErrorMessageRequired());
    }
}
