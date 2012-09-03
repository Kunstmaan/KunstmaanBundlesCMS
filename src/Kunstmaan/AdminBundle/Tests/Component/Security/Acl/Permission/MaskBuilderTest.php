<?php

namespace Kunstmaan\AdminBundle\Tests\Component\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\MaskBuilder;

class MaskBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getInvalidConstructorData
     */
    public function testConstructorWithNonInteger($invalidMask)
    {
        new MaskBuilder($invalidMask);
    }

    public function getInvalidConstructorData()
    {
        return array(
            array(234.463),
            array('asdgasdf'),
            array(array()),
            array(new \stdClass()),
        );
    }

    public function testConstructorWithoutArguments()
    {
        $builder = new MaskBuilder();

        $this->assertEquals(0, $builder->get());
    }

    public function testConstructor()
    {
        $builder = new MaskBuilder(123456);

        $this->assertEquals(123456, $builder->get());
    }

    public function testAddAndRemove()
    {
        $builder = new MaskBuilder();

        $builder
            ->add('view')
            ->add('eDiT')
            ->add('puBLisH')
        ;
        $mask = $builder->get();

        $this->assertEquals(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
        $this->assertEquals(MaskBuilder::MASK_EDIT, $mask & MaskBuilder::MASK_EDIT);
        $this->assertEquals(MaskBuilder::MASK_PUBLISH, $mask & MaskBuilder::MASK_PUBLISH);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_DELETE);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_UNPUBLISH);

        $builder->remove('edit')->remove('PUblish');
        $mask = $builder->get();
        $this->assertEquals(0, $mask & MaskBuilder::MASK_EDIT);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_PUBLISH);
        $this->assertEquals(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
    }

    public function testGetPattern()
    {
        $builder = new MaskBuilder;
        $this->assertEquals(MaskBuilder::ALL_OFF, $builder->getPattern());

        $builder->add('view');
        $this->assertEquals(str_repeat('.', 31).'V', $builder->getPattern());

        $builder->add('publish');
        $this->assertEquals(str_repeat('.', 27).'P...V', $builder->getPattern());

        $builder->add(1 << 10);
        $this->assertEquals(str_repeat('.', 21).MaskBuilder::ON.'.....P...V', $builder->getPattern());
    }

    public function testReset()
    {
        $builder = new MaskBuilder();
        $this->assertEquals(0, $builder->get());

        $builder->add('view');
        $this->assertTrue($builder->get() > 0);

        $builder->reset();
        $this->assertEquals(0, $builder->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddWithInvalidMask()
    {
        $builder = new MaskBuilder();
        $builder->add(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveWithInvalidMask()
    {
        $builder = new MaskBuilder();
        $builder->remove(null);
    }

    public function testGetCode()
    {
        $code = MaskBuilder::getCode(MaskBuilder::MASK_DELETE);
        $this->assertEquals(MaskBuilder::CODE_DELETE, $code);

        $code = MaskBuilder::getCode(MaskBuilder::MASK_UNPUBLISH);
        $this->assertEquals(MaskBuilder::CODE_UNPUBLISH, $code);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCodeWithInvalidMask()
    {
        $code = MaskBuilder::getCode(null);
    }

    public function testHas()
    {
        $builder = new MaskBuilder();
        $builder->add('edit')
            ->add('view');

        $this->assertEquals(true, $builder->has(MaskBuilder::MASK_EDIT));
        $this->assertEquals(true, $builder->has('view'));
        $this->assertEquals(false, $builder->has(MaskBuilder::MASK_UNPUBLISH));
        $this->assertEquals(false, $builder->has('publish'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHasWithInvalidMask()
    {
        $builder = new MaskBuilder();
        $builder->add('edit')
            ->add('view');

        $builder->has(null);
    }

}
