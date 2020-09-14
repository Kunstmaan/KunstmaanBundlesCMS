<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'errors.password.dontmatch',
                'first_options' => array(
                    'label' => 'settings.user.password',
                ),
                'second_options' => array(
                    'label' => 'settings.user.repeatedpassword',
                ),
            ))
            ->add('submit', SubmitType::class, [
                'label' => 'Reset password',
                'attr' => ['class' => 'btn btn-brand btn-block btn--raise-on-hover'],
            ]);
    }
}
