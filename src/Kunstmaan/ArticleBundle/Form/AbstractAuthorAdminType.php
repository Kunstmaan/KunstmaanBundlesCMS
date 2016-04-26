<?php

namespace Kunstmaan\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractAuthorAdminType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'article.author.form.name.label',
        ));
        $builder->add('link', null, array(
            'label' => 'article.author.form.link.label',
        ));
    }

    function getBlockPrefix()
    {
        return "abstactauthor_form";
    }

}
