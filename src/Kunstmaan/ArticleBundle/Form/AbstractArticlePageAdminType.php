<?php

namespace Kunstmaan\ArticleBundle\Form;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The admin type for abstract article pages
 */
class AbstractArticlePageAdminType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add(
            'date',
            DateTimeType::class,
            [
                'label' => 'article.form.date.label',
                'required' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'dd/MM/yyyy',
            ]
        );

        $builder->add('summary', TextType::class, [
            'label' => 'article.form.summary.label',
        ]);
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\ArticleBundle\Entity\AbstractArticlePage',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'AbstractArticlePage';
    }
}
