<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\ArticleBundle\Form\AbstractArticleOverviewPageAdminType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The admin type for {{ entity_class }}overview pages
 */
class {{ entity_class }}OverviewPageAdminType extends AbstractArticleOverviewPageAdminType
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
	    'data_class' => '{{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
	return '{{ entity_class|lower }}_overview_page_type';
    }
}
