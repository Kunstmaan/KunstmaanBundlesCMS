<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username');
        $builder->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => $options['password_required'],
                'invalid_message' => "The passwords don't match!"));
        $builder->add('email');
        $builder->add('enabled', 'checkbox', array('required' => false));
        $builder->add('groups', null, array(
            'expanded'  => false //change to true to expand to checkboxes
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'password_required' => false,
        );
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getAllowedOptionValues(array $options)
    {
        return array(
            'password_required' => array(
                true,
                false
            ),
        );
    }
}
