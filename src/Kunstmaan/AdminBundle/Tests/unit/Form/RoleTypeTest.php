<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\RoleType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;

/**
 * Class RoleTypeTest
 */
class RoleTypeTest extends PHPUnit_Framework_TestCase
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
