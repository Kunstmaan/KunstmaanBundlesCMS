<?php

namespace Kunstmaan\ViewBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SearchPageAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title');
    }

    public function getName()
    {
        return 'page';
    }
}