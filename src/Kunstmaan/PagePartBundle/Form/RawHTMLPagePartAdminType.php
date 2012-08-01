<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * class to add content to a raw html pagepart
 *
 */
class RawHTMLPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array('required' => false, 'attr' => array( "style"=> "width: 600px",'rows'=>32 )))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart',
        );
    }

    public function getName()
    {
        return 'kunstmaan_pagepartbundle_rawhtmlpageparttype';
    }
}
