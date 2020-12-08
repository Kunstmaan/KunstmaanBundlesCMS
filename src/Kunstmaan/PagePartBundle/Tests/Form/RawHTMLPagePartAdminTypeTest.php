<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Form\RawHTMLPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

class RawHTMLPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var RawHTMLPagePartAdminType
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new RawHTMLPagePartAdminType();
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
        $this->assertEquals($resolve['data_class'], 'Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart');
    }
}
