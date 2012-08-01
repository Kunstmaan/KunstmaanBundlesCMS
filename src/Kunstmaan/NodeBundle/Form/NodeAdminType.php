<?php

namespace Kunstmaan\AdminNodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class NodeAdminType extends AbstractType
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       	$builder->add('hiddenfromnav', "checkbox");
    }

    public function getName()
    {
        return 'node';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\AdminNodeBundle\Entity\Node',
        );
    }
}