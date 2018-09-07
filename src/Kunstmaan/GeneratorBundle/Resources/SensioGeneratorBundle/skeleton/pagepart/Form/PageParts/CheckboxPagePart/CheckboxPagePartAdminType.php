<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {{ pagepart }}AdminType
 */
class {{ pagepart }}AdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'label',
                TextType::class,
                [
                    'label' => 'kuma_form.form.checkbox_page_part.label.label',
                    'required' => true,
                ]
            )
            ->add(
                'required',
                CheckboxType::class,
                [
                    'label' => 'kuma_form.form.checkbox_page_part.required.label',
                    'required' => false,
                ]
            )
            ->add(
                'errormessage_required',
                TextType::class,
                [
                    'label' => 'kuma_form.form.checkbox_page_part.errormessage_required.label',
                    'required' => false,
                ]
            )
            ->add(
                'internalName',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'kuma_form.form.form_page_part.internal_name',
                ]
            );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return '{{ pagepart|lower }}type';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => '{{ namespace }}\Entity\PageParts\{{ pagepart }}',
            ]
        );
    }
}
