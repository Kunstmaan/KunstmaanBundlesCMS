<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;

/**
 * Class TextPagePartAdminTypeTest
 * @package Tests\Kunstmaan\PagePartBundle\Form
 */
class TextPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var TextPagePartAdminType
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new TextPagePartAdminType();
    }

    public function testBuildForm()
    {
        $this->object->buildForm($this->builder, array());
        $this->builder->get('content');
    }

    public function testConfigureOptions()
    {
        $this->object->configureOptions($this->resolver);
        $resolve = $this->resolver->resolve();
        $this->assertEquals($resolve["data_class"], 'Kunstmaan\PagePartBundle\Entity\TextPagePart');
    }
}
