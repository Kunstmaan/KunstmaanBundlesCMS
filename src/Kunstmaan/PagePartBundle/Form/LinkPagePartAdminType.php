<?php

namespace Kunstmaan\PagePartBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * LinkPagePartAdminType
 */
class LinkPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'urlchooser', array('label' => 'pagepart.link.choose', 'required' => false))
            ->add('openinnewwindow', 'checkbox', array('label' => 'pagepart.link.openinnewwindow', 'required' => false))
            ->add('text', null, array('label' => 'pagepart.link.text', 'required' => false));
    }

    /**
     * @assert () == 'kunstmaan_pagepartbundle_linkpageparttype'
     *
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_linkpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart',
        ));
    }
}
