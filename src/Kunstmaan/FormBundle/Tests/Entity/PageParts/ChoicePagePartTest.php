<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Symfony\Component\Form\FormBuilder;
use Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class ChoicePagePartTest extends TestCase
{
    /**
     * @var ChoicePagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ChoicePagePart();
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testAdaptForm()
    {
        $object = $this->object;
        $object->setChoices('choice1\nchoice2');
        $object->setRequired(true);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder
            ->method('getData')
            ->willReturn([]);

        $fields = new \ArrayObject();

        $this->assertCount(0, $fields);
        $object->setErrorMessageRequired('invalid!');
        /* @var FormBuilderInterface $formBuilder */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(ChoicePagePartAdminType::class, $this->object->getDefaultAdminType());
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

    public function testGettersAndSetters()
    {
        $object = $this->object;
        $value = 'test';
        $object->setEmptyValue($value);
        $object->setRequired(true);
        $object->setErrorMessageRequired('fix your code!');

        $this->assertSame($value, $object->getEmptyValue());
        $this->assertTrue($object->getRequired());
        $this->assertSame('fix your code!', $object->getErrorMessageRequired());
    }
}
