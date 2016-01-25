<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\ArticleBundle\Form\AbstractArticleOverviewPageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The admin type for {{ entity_class }}overview pages
 */
class {{ entity_class }}OverviewPageAdminType extends AbstractArticleOverviewPageAdminType
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
	    'data_class' => '{{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
	return '{{ entity_class|lower }}_overview_page_type';
    }
}
