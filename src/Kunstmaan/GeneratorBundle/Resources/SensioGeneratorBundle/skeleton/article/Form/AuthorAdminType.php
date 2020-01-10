<?php

namespace {{namespace}}\Form;

use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ entity_class }}AuthorAdminType extends AbstractAuthorAdminType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
	        'data_class' => '{{ namespace }}\Entity\{{ entity_class }}Author'
        ]);
    }
}
