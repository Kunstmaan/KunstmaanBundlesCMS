<?php

namespace {{ namespace }}\Form\Pages;

use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class {{ entity_class }}PageAdminType extends AbstractArticlePageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        //%PageAdminTypePartial.php.twig%
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
	        'data_class' => '{{ namespace }}\Entity\Pages\{{ entity_class }}Page'
        ]);
    }
}
