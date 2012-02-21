<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RoleType extends AbstractType {
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('role');
    }

    public function getName() {
        return 'role';
    }
}