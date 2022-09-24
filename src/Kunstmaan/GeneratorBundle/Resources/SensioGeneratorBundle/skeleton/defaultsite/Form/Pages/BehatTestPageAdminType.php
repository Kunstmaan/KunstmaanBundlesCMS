<?php

namespace {{ namespace }}\Form\Pages;

use {{ namespace }}\Entity\Pages\BehatTestPage;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BehatTestPageAdminType extends PageAdminType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BehatTestPage::class,
        ]);
    }
}
