<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * {{ pagepart }}AdminType
 */
class {{ pagepart }}AdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('linkUrl', URLChooserType::class, array(
            'required' => true
        ));
        $builder->add('linkText', TextType::class, array(
            'required' => true
        ));
        $builder->add('linkNewWindow', CheckboxType::class, array(
            'required' => false,
        ));
        $builder->add('type', ChoiceType::class, array(
            'choices' => array_combine({{ pagepart }}::$types, {{ pagepart }}::$types),
            'placeholder' => false,
            'required' => true,
                'choices_as_values' => true
        ));
        $builder->add('size', ChoiceType::class, array(
            'choices' => array_combine({{ pagepart }}::$sizes, {{ pagepart }}::$sizes),
            'placeholder' => false,
            'required' => true,
                'choices_as_values' => true
        ));
        $builder->add('position', ChoiceType::class, array(
            'choices' => array_combine({{ pagepart }}::$positions, {{ pagepart }}::$positions),
            'placeholder' => false,
            'required' => true,
                'choices_as_values' => true
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return '{{ pagepart|lower }}type';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\{{ namespace }}\Entity\PageParts\{{ pagepart }}'
        ));
    }
}
