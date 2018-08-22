<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ entity_class }}AuthorAdminType extends AbstractAuthorAdminType
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
	        'data_class' => '{{ namespace }}\Entity\{{ entity_class }}Author'
        ));
    }

    /**
     * @return string
     */
    function getBlockPrefix()
    {
	    return '{{ entity_class|lower }}_author_type';
    }
}
