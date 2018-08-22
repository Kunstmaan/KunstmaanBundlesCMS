<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractCategoryAdminType;

/**
 * The type for Category
 */
class {{ entity_class }}CategoryAdminType extends AbstractCategoryAdminType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
{
    return '{{ entity_class|lower }}_category_form';
}
}
