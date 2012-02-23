<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * class to define the form to upload a picture
 *
 */
class LinkPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('url', 'urlchooser', array( 'data_class' =>'Kunstmaan\PagePartBundle\Entity\LinkPagePart', 'required' => false))
            ->add('openinnewwindow', 'checkbox', array('required' => false))
            ->add('text', null, array('required' => false))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart',
        );
    }

    public function getName()
    {
        return 'kunstmaan_pagepartbundle_linkpageparttype';
    }
}

?>