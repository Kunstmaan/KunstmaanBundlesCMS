<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

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
        $this->assertEquals('Kunstmaan\PagePartBundle\Entity\ToTopPagePart', $resolve['data_class']);
    }
}
