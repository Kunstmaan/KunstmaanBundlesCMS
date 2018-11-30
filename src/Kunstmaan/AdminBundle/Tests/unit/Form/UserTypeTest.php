<?php

namespace Kunstmaan\AdminBundle\Tests\Form;

use Kunstmaan\AdminBundle\Form\UserType;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserTypeTest
 */
class UserTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $type = new UserType();

        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->willReturn(true);
        $resolver->expects($this->once())
            ->method('addAllowedValues')
            ->willReturn(true);

        /* @var OptionsResolver $resolver */
        $type->configureOptions($resolver);
        $type->setCanEditAllFields(true);
        $this->assertEquals(FormType::class, $type->getParent());
        $this->assertEquals('user', $type->getBlockPrefix());

        $builder = $this->createMock(FormBuilder::class);

        $builder->expects($this->exactly(6))
            ->method('add')
            ->willReturn($builder);

        /* @var FormBuilder $builder */
        $type->buildForm($builder, [
            'langs' => [
                'en', 'nl', 'es', 'de', 'fr',
            ],
            'password_required' => true,
            'can_edit_all_fields' => true,
        ]);
    }
}
