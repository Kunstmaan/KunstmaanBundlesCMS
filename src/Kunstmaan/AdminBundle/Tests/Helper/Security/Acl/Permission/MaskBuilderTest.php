<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use PHPUnit\Framework\TestCase;

/**
 * MaskBuilderTest
 */
class MaskBuilderTest extends TestCase
{
    /**
     * @dataProvider getInvalidConstructorData
     */
    public function testSlugify($invalidMask)
    {
        $this->expectException(\InvalidArgumentException::class);
        new MaskBuilder($invalidMask);
    }

    public function testGetCodeThrowsException()
    {
        $this->expectException(\Exception::class);
        $builder = new MaskBuilder();
        $builder->getCode(MaskBuilder::MASK_IDDQD);
    }

    public function testResolveMaskThrowsException()
    {
        $this->expectException(\Exception::class);
        $builder = new MaskBuilder();
        $builder->resolveMask('fail!');
    }

    /**
     * Provides data to the {@link testSlugify} function
     */
    public function getInvalidConstructorData(): \Iterator
    {
        yield [234.463];
        yield ['asdgasdf'];
        yield [[]];
        yield [new \stdClass()];
    }

    public function testConstructorWithoutArguments()
    {
        $builder = new MaskBuilder();

        $this->assertSame(0, $builder->get());
    }

    public function testConstructor()
    {
        $builder = new MaskBuilder(123456);

        $this->assertSame(123456, $builder->get());
    }

    public function testAddAndRemove()
    {
        $builder = new MaskBuilder();

        $builder
            ->add('view')
            ->add('eDiT')
            ->add('puBLisH');
        $mask = $builder->get();

        $this->assertSame(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
        $this->assertSame(MaskBuilder::MASK_EDIT, $mask & MaskBuilder::MASK_EDIT);
        $this->assertSame(MaskBuilder::MASK_PUBLISH, $mask & MaskBuilder::MASK_PUBLISH);
        $this->assertSame(0, $mask & MaskBuilder::MASK_DELETE);
        $this->assertSame(0, $mask & MaskBuilder::MASK_UNPUBLISH);

        $builder->remove('edit')->remove('PUblish');
        $mask = $builder->get();
        $this->assertSame(0, $mask & MaskBuilder::MASK_EDIT);
        $this->assertSame(0, $mask & MaskBuilder::MASK_PUBLISH);
        $this->assertSame(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
    }

    public function testGetPattern()
    {
        $builder = new MaskBuilder();
        $this->assertSame(MaskBuilder::ALL_OFF, $builder->getPattern());

        $builder->add('view');
        $this->assertSame(str_repeat('.', 31) . 'V', $builder->getPattern());

        $builder->add('publish');
        $this->assertSame(str_repeat('.', 27) . 'P...V', $builder->getPattern());

        $builder->add(1 << 10);
        $this->assertSame(str_repeat('.', 21) . MaskBuilder::ON . '.....P...V', $builder->getPattern());
    }

    public function testReset()
    {
        $builder = new MaskBuilder();
        $this->assertSame(0, $builder->get());

        $builder->add('view');
        $this->assertGreaterThan(0, $builder->get());

        $builder->reset();
        $this->assertSame(0, $builder->get());
    }

    public function testAddWithInvalidMask()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new MaskBuilder();
        $builder->add(null);
    }

    public function testRemoveWithInvalidMask()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new MaskBuilder();
        $builder->remove(null);
    }

    public function testGetCode()
    {
        $code = MaskBuilder::getCode(MaskBuilder::MASK_DELETE);
        $this->assertSame(MaskBuilder::CODE_DELETE, $code);

        $code = MaskBuilder::getCode(MaskBuilder::MASK_UNPUBLISH);
        $this->assertSame(MaskBuilder::CODE_UNPUBLISH, $code);
    }

    public function testGetCodeWithInvalidMask()
    {
        $this->expectException(\InvalidArgumentException::class);
        MaskBuilder::getCode(null);
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

    public function testHasWithInvalidMask()
    {
        $this->expectException(\InvalidArgumentException::class);
        $builder = new MaskBuilder();
        $builder->add('edit')
            ->add('view');

        $builder->has(null);
    }
}
