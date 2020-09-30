<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control form-group--icon-in-control__form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Reset password',
                'attr' => ['class' => 'btn btn-primary btn--raise-on-hover'],
            ]);
    }
}
