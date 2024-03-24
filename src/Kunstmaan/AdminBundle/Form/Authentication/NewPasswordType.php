<?php

namespace Kunstmaan\AdminBundle\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

final class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'errors.password.dontmatch',
                'first_options' => [
                    'label' => 'settings.user.password',
                ],
                'second_options' => [
                    'label' => 'settings.user.repeatedpassword',
                ],
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ])
        ;
    }
}
