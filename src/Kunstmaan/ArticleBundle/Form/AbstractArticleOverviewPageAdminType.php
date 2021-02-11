<?php

namespace Kunstmaan\ArticleBundle\Form;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The admin type for abstract article overview pages
 */
class AbstractArticleOverviewPageAdminType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\ArticleBundle\Entity\AbstractOverviewArticlePage',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'AbstractArticleOverviewPage';
    }
}
