<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Form\TocPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

class TocPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var TocPagePartAdminType
     */
    protected $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new TocPagePartAdminType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->never())->method('add');

        $this->object->buildForm($builder, []);
    }

    public function testConfigureOptions()
    {
        $this->object->configureOptions($this->resolver);
        $resolve = $this->resolver->resolve();
        $this->assertEquals('Kunstmaan\PagePartBundle\Entity\TocPagePart', $resolve['data_class']);
    }
}
