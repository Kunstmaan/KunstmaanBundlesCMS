<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;

class GroupType extends AbstractType {
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name');
        $builder->add('rolescollection', null, array(
            'expanded'  => false, //change to true to expand to checkboxes
        ));
    }

    public function getName() {
        return 'group';
    }
}