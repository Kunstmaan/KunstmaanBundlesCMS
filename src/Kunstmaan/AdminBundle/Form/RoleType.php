<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;

class RoleType extends AbstractType {
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('role');
    }

    public function getName() {
        return 'role';
    }
}