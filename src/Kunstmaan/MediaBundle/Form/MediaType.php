<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', 'file')
        ;
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_filetype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\MediaBundle\Helper\MediaHelper',
        );
    }
}