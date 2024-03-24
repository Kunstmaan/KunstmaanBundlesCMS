<?php

namespace Kunstmaan\AdminBundle\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ForcedChangePasswordForm extends AbstractType
{
    /** @var string */
    private $userClass;

    public function __construct(string $userClass)
    {
        $this->userClass = $userClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'security.change_password.form.current_password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new UserPassword(),
                ],
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'label' => 'security.change_password.form.new_password',
                ],
                'second_options' => [
                    'label' => 'security.change_password.form.new_password_confirmation',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', $this->userClass);
    }
}
