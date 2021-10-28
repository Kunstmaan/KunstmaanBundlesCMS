<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use {{ namespace }}\Entity\PageParts\LegalCookiesPagePart;


/**
 * LegalCookiesPagePartAdminType
 */
class LegalCookiesPagePartAdminType extends \Symfony\Component\Form\AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'legal_cookiespageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LegalCookiesPagePart::class,
            ]
        );
    }
}
