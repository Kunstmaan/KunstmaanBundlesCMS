<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

class TextPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var TextPagePartAdminType
     */
    protected $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new TextPagePartAdminType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())->method('add');

        $this->object->buildForm($builder, []);
    }

    public function testConfigureOptions()
    {
        $this->object->configureOptions($this->resolver);
        $resolve = $this->resolver->resolve();
        $this->assertEquals('Kunstmaan\PagePartBundle\Entity\TextPagePart', $resolve['data_class']);
    }
}
