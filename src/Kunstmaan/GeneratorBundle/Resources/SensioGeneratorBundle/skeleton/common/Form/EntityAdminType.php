<?php

namespace {{ namespace }}\Form\{{ entity_prefix }};

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Kunstmaan\MediaBundle\Validator\Constraints as Assert;

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
{% if field.mimeTypes != null or (field.mediaType == 'image' and (field.minHeight != null or field.maxHeight != null or field.minWidth != null or field.maxWidth or null)) %}
            'constraints' => array(new Assert\Media(array(
{% if field.mimeTypes != null %}
                'mimeTypes' => array({% for type in field.mimeTypes %}'{{ type }}',{% endfor %}),
{% endif %}
{% endif %}
{% if field.mediaType is defined and field.mediaType == 'image' %}
{% if field.minHeight != null %}
                'minHeight' => '{{ field.minHeight }}',
{% endif %}
{% if field.maxHeight != null %}
                'maxHeight' => '{{ field.maxHeight }}',
{% endif %}
{% if field.minWidth != null %}
                'minWidth' => '{{ field.minWidth }}',
{% endif %}
{% if field.maxWidth != null %}
                'maxWidth' => '{{ field.maxWidth }}',
{% endif %}
{% endif %}
{% if field.mimeTypes != null or (field.mediaType == 'image' and (field.minHeight != null or field.maxHeight != null or field.minWidth != null or field.maxWidth or null)) %}
            ))),
{% endif %}
{% endif %}
{% if key == 'rich_text' %}            'attr' => array('rows' => 10, 'cols' => 600, 'class' => 'js-rich-editor rich-editor', 'height' => 140),
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
