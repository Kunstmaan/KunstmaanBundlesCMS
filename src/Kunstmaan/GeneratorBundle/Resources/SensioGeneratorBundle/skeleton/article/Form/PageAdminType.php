<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The admin type for {{ entity_class }} pages
 */
class {{ entity_class }}PageAdminType extends AbstractArticlePageAdminType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'author'
        );
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
	    'data_class' => '{{ namespace }}\Entity\Pages\{{ entity_class }}Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
	return '{{ entity_class|lower }}_page_type';
    }
}
