<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\ArticleBundle\Form\AbstractArticleOverviewPageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ entity_class }}OverviewPageAdminType extends AbstractArticleOverviewPageAdminType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
	        'data_class' => '{{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage'
        ]);
    }
}
