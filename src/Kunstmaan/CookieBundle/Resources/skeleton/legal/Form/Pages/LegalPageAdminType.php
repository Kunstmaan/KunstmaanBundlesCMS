<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use {{ namespace }}\Entity\Pages\LegalPage;

/**
 * Class LegalPageAdminType
 */
class LegalPageAdminType extends PageAdminType
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LegalPage::class,
            ]
        );
    }

    /**
     * @assert () == 'homepage'
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'legalpage';
    }
}
