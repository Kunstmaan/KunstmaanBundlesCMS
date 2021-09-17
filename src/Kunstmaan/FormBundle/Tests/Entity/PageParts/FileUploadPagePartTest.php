<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;
use Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart;
use Kunstmaan\FormBundle\Form\FileUploadPagePartAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class FileUploadPagePartTest extends TestCase
{
    /**
     * @var FileUploadPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new FileUploadPagePart();
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

        $object->setErrorMessageRequired('this is required');
        $this->assertEquals(0, count($fields));
        /* @var FormBuilderInterface $formBuilder */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
        $this->assertEquals('this is required', $object->getErrorMessageRequired());
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(FileUploadPagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testGetSetRequired()
    {
        $obj = $this->object;
        $obj->setRequired(true);
        $this->assertTrue($obj->getRequired());
    }
}
