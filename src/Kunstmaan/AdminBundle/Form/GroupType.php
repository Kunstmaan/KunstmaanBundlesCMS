<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('rolescollection', null, array(
            'expanded'  => false, //change to true to expand to checkboxes
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
