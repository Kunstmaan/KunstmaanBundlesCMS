<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class {{ entity_class }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
{% for field in fields %}
        $builder->add('{{ field }}');
{% endfor %}
    }
}
