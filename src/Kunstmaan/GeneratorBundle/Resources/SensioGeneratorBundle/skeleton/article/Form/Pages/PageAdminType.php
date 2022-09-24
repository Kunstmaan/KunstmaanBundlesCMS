<?php

namespace {{ namespace }}\Form\Pages;

use {{ namespace }}\Entity\Pages\{{ entity_class }}Page;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ entity_class }}PageAdminType extends AbstractArticlePageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        //%PageAdminTypePartial.php.twig%
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ entity_class }}Page::class,
        ]);
    }
}
