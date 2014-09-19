<?php

namespace Kunstmaan\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * NodeMenuTabAdminType
 */
class NodeMenuTabAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('hiddenFromNav', 'checkbox', array('label' => 'Hidden from menu', 'required' => false));
        $builder->add('internalName', 'text', array('label' => 'Internal name', 'required' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\NodeBundle\Entity\Node',
        ));
    }
}
