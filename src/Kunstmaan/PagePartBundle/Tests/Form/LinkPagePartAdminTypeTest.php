<?php

namespace Kunstmaan\PagePartBundle\Tests\Form;

use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-08-20 at 13:19:18.
 */
class LinkPagePartAdminTypeTest extends PagePartAdminTypeTestCase
{
    /**
     * @var LinkPagePartAdminType
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new LinkPagePartAdminType();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType::buildForm
     */
    public function testBuildForm()
    {
        $this->object->buildForm($this->builder, array());
        $this->builder->get('url');
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType::configureOptions
     */
    public function testConfigureOptions()
    {
        $this->object->configureOptions($this->resolver);
        $resolve = $this->resolver->resolve();
        $this->assertEquals($resolve['data_class'], 'Kunstmaan\PagePartBundle\Entity\LinkPagePart');
    }
}
