<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('media', 'file')
        ;
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_filetype';
    }
}