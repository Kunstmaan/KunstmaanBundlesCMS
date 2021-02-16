<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\GroupType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class GroupTypeTest extends TestCase
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
