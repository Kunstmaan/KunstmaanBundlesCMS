<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Symfony\Component\Form\FormBuilder;
use Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart;
use Kunstmaan\FormBundle\Form\SingleLineTextPagePartAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class SingleLineTextPagePartTest extends TestCase
{
    /**
     * @var SingleLineTextPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new SingleLineTextPagePart();
    }

    public function testSetGetRegex()
    {
        $object = $this->object;
        $regex = '.*example.*';
        $object->setRegex($regex);
        $this->assertSame($regex, $object->getRegex());
    }

    public function testSetErrorMessageRegex()
    {
        $object = $this->object;
        $message = 'Some example error message';
        $object->setErrorMessageRegex($message);
        $this->assertSame($message, $object->getErrorMessageRegex());
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
        $object->setRequired(true);
        $object->setRegex('.*example.*');

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder
            ->method('getData')
            ->willReturn([]);

        $fields = new \ArrayObject();

        $object->setErrorMessageRequired('required');
        $object->setErrorMessageRegex('regex');
        $this->assertCount(0, $fields);
        /* @var FormBuilderInterface $formBuilder */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertGreaterThan(0, count($fields));
        $this->assertTrue($object->getRequired());

        $this->assertSame('required', $object->getErrorMessageRequired());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(SingleLineTextPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
