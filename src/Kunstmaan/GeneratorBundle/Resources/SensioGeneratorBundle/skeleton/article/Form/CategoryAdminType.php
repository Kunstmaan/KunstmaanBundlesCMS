<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractCategoryAdminType;

class {{ entity_class }}CategoryAdminType extends AbstractCategoryAdminType
{
    public function getBlockPrefix(): string
{
    return '{{ entity_class|lower }}_category_form';
}
}
