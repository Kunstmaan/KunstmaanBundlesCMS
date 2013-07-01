<?php

namespace {{ namespace }}\Form\Pageparts;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * {{ className }}
 */
class {{ className }} extends AbstractType
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
{% for field in fields %}
        $builder->add(
            '{{ field.fieldName }}',
            '{{ field.formType }}',
            array(
{% if field.formType == 'media' %}                'pattern' => 'KunstmaanMediaBundle_chooser',
{% endif %}
{% if field.nullable is defined and field.nullable %}                'required' => false,
{% endif %}
                'label' => '{{ field.fieldName }}'
            )
        );
{% endfor %}
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