<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use {{ namespace }}\Entity\Pages\LegalFolderPage;

/**
 * Class LegalFolderPageAdminType
 */
class LegalFolderPageAdminType extends PageAdminType
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
                'data_class' => LegalFolderPage::class,
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
        return 'legalfolderpage';
    }
}
