<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractTagAdminType;

class {{ entity_class }}TagAdminType extends AbstractTagAdminType
{
    public function getBlockPrefix(): string
    {
        return '{{ entity_class|lower }}_tag_form';
    }
}
