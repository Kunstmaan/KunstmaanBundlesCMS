<?php

namespace Kunstmaan\PagePartBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * class to add content to a raw html pagepart
 *
 */
class RawHTMLPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'textarea', array('label' => 'pagepart.html.content', 'required' => false, 'attr' => array("style" => "width: 600px", 'rows' => 32)));
    }

    /**
     * @assert () == 'kunstmaan_pagepartbundle_rawhtmlpageparttype'
     *
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_rawhtmlpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart',
        ));
    }
}
