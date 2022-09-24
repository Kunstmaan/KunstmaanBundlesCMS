<?php

namespace {{ namespace }}\Form\Pages;

use {{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage;
use Kunstmaan\ArticleBundle\Form\AbstractArticleOverviewPageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ entity_class }}OverviewPageAdminType extends AbstractArticleOverviewPageAdminType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ entity_class }}OverviewPage::class,
        ]);
    }
}
