<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The admin type for behat test pages
 */
class BehatTestPageAdminType extends PageAdminType
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ namespace }}\Entity\Pages\BehatTestPage'
        ));
    }

    /**
     * @assert () == 'behat_test_page'
     *
     * @return string
     */
    public function getName()
    {
        return 'behat_test_page';
    }
}
