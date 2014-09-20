<?php

namespace {{ namespace }}\Form\{{ entity_prefix }};

use Symfony\Component\Form\FormBuilderInterface;use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * {{ className }}
 */
class {{ className }} extends {{ extend_class }}
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}{% for field in fieldArray %}
        $builder->add('{{ field.fieldName }}', '{{ field.formType }}', array(
{% if field.formType == 'media' %}            'pattern' => 'KunstmaanMediaBundle_chooser',
{% endif %}
{% if field.mediaType is defined and field.mediaType != 'none' %}            'mediatype' => '{{ field.mediaType }}',
{% endif %}
{% if key == 'rich_text' %}            'attr' => array('rows' => 10, 'cols' => 600, 'class' => 'rich_editor'),
{% endif %}
{% if key == 'multi_line' %}            'attr' => array('rows' => 10, 'cols' => 600),
{% endif %}
{% if key == 'single_ref' %}            'class' => '{{ field.targetEntity }}',
            'expanded' => false,
            'multiple' => false,
{% endif %}
{% if key == 'datetime' %}            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'date_format' => 'dd/MM/yyyy',
{% endif %}
{% if key == 'multi_ref' %}            'class' => '{{ field.targetEntity }}',
            'expanded' => true,
            'multiple' => true,
{% endif %}
{% if field.nullable is defined and field.nullable %}            'required' => false,
{% endif %}
        ));
{% endfor %}{% endfor %}{% endfor %}
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return '{{ name }}';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ entity }}'
        ));
    }
}
