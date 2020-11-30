<?php

namespace Kunstmaan\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractTagAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, [
            'label' => 'article.tag.form.name.label',
            'required' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'abstacttag_form';
    }
}
