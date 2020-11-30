<?php

namespace Kunstmaan\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractCategoryAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, [
            'label' => 'article.category.form.name.label',
            'required' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'abstactcategory_form';
    }
}
