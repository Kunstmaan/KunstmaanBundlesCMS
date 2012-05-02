<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SubFolderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
        ;
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_subFolderType';
    }
}