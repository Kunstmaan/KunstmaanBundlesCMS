<?php

namespace Kunstmaan\AdminBundle\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'security.resetting.reset',
                'attr' => ['class' => 'btn btn-primary btn--raise-on-hover'],
            ]);
    }
}
