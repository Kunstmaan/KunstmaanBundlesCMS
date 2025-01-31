<?php

declare(strict_types=1);

namespace Kunstmaan\NodeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InternalURLSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('input', HiddenType::class, [
            'label' => false,
        ]);
        $builder->add('url', TextType::class, [
            'label' => false,
            'disabled' => true,
            'mapped' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'internal_url_selector';
    }
}
