<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

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

        //%PageAdminTypePartial.php.twig%
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
	        'data_class' => '{{ namespace }}\Entity\Pages\{{ entity_class }}Page'
        ));
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
	    return '{{ entity_class|lower }}_page_type';
    }
}
