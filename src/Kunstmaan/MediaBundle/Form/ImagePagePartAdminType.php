<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ImagePagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title', null, array('required' => false));
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_imagepageparttype';
    }
}