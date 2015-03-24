<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class {{ entity_class }}AuthorAdminType extends AbstractAuthorAdminType
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ namespace }}\Entity\{{ entity_class }}Author'
        ));
    }

    /**
     * @return string
     */
    function getName()
    {
        return '{{ entity_class|lower }}_author_type';
    }
}
