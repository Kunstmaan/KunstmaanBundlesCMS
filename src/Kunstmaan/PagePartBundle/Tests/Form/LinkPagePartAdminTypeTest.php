<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Entity\LinkPagePart;
use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;
use Symfony\Component\Form\FormBuilderInterface;

class LinkPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var LinkPagePartAdminType
     */
    protected $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new LinkPagePartAdminType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->exactly(3))->method('add')->willReturnSelf();

        $this->object->buildForm($builder, []);
    }

    public function testConfigureOptions()
    {
        $this->object->configureOptions($this->resolver);
        $resolve = $this->resolver->resolve();
        $this->assertSame(LinkPagePart::class, $resolve['data_class']);
    }
}
