<?php

namespace {{ namespace }}\Form;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class {{ entity_class }}AdminType extends AbstractType
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilder $builder, array $options) 
    {
        $builder
    	{%- for field in fields %}
			->add('{{ field }}');
        {%- endfor %}
        ;
    }

    function getName() 
    {
        return "{{ entity_class }}";
    }
}