<?php

namespace {{ namespace }}\Form\Pages;

use {{ namespace }}\Entity\Pages\HomePage;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomePageAdminType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HomePage::class,
        ]);
    }
}
