<?php

namespace Kunstmaan\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractAuthorAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'article.author.form.name.label',
        ));
        $builder->add('link', TextType::class, array(
            'label' => 'article.author.form.link.label',
        ));
    }

    public function getBlockPrefix()
    {
        return 'abstactauthor_form';
    }
}
