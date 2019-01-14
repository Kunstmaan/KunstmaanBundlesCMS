<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\GroupType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;

/**
 * Class GroupTypeTest
 */
class GroupTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $type = new GroupType();

        $builder = $this->createMock(FormBuilder::class);

        $builder->expects($this->exactly(2))
            ->method('add')
            ->willReturn($builder);

        /* @var FormBuilder $builder */
        $type->buildForm($builder, []);

        $this->assertEquals('group', $type->getBlockPrefix());
    }
}
