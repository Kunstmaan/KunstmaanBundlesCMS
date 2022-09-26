<?php

namespace {{namespace}}\Form;

use {{ namespace }}\Entity\{{ entity_class }}Author;
use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ entity_class }}AuthorAdminType extends AbstractAuthorAdminType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ entity_class }}Author::class,
        ]);
    }
}
