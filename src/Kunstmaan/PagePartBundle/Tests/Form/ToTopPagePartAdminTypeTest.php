<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Entity\ToTopPagePart;
use Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

class ToTopPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var ToTopPagePartAdminType
     */
    protected $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new ToTopPagePartAdminType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->never())->method('add');

        $this->object->buildForm($this->builder, []);
    }

    public function testConfigureOptions()
    {
        $this->object->configureOptions($this->resolver);
        $resolve = $this->resolver->resolve();
        $this->assertSame(ToTopPagePart::class, $resolve['data_class']);
    }
}
