<?php

namespace {{ namespace }}\Form\{{ entity_prefix }};

use Kunstmaan\MediaBundle\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ className }} extends {{ extend_class }}
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
{% for fieldSet in fields %}{% for key, fieldArray in fieldSet %}{% for field in fieldArray %}
        $builder->add('{{ field.fieldName }}', '{{ field.formType }}', [
{% if field.mediaType is defined and field.mediaType != 'none' %}            'mediatype' => '{{ field.mediaType }}',
{% if field.mimeTypes != null or (field.mediaType == 'image' and (field.minHeight != null or field.maxHeight != null or field.minWidth != null or field.maxWidth or null)) %}
            'constraints' => [new Assert\Media([
{% if field.mimeTypes != null %}
                'mimeTypes' => [
                {% for type in field.mimeTypes %}
    '{{ type }}',
                {% endfor %}],
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
            ])],
{% endif %}
{% endif %}
{% if key == 'rich_text' %}            'attr' => ['rows' => 10, 'cols' => 600, 'class' => 'js-rich-editor rich-editor', 'height' => 140],
{% endif %}
{% if key == 'multi_line' %}            'attr' => ['rows' => 10, 'cols' => 600],
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
        ]);
{% endfor %}{% endfor %}{% endfor %}
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '{{ entity }}',
        ]);
    }
}
