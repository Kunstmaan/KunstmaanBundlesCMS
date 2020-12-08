<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\RoleType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class RoleTypeTest extends TestCase
{
    public function testMethods()
    {
        $type = new RoleType();

        $builder = $this->createMock(FormBuilder::class);

        $builder->expects($this->once())
            ->method('add')
            ->willReturn(true);

        /* @var FormBuilder $builder */
        $type->buildForm($builder, []);

        $this->assertEquals('role', $type->getBlockPrefix());
    }
}
