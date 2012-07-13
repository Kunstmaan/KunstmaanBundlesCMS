<?php

namespace {{ namespace }}\Form;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class {{ entity_class }}AdminType extends AbstractType {

    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    public function buildForm(FormBuilder $builder, array $options) {
    	{%- for field in fields %}
			$builder->add('{{ field }}');
        {%- endfor %}
    }

    function getName() {
        return "{{ entity_class }}";
    }
}