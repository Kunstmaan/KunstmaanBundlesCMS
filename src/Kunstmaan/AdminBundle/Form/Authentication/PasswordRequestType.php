<?php

namespace Kunstmaan\AdminBundle\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PasswordRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'settings.user.email',
                'attr' => ['class' => 'form-control form-group--icon-in-control__form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'settings.user.password',
                'attr' => ['class' => 'btn btn-primary btn--raise-on-hover'],
            ]);
    }
}
