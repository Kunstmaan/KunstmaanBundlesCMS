<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractTagAdminType;

/**
 * The type for Tag
 */
class {{ entity_class }}TagAdminType extends AbstractTagAdminType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return '{{ entity_class|lower }}_tag_form';
    }
}
