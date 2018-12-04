<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class represents the type for the ChoicePagePart
 */
class ChoicePagePartAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'kuma_form.form.choice_page_part.label.label',
                'required' => true,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'kuma_form.form.choice_page_part.required.label',
                'required' => false,
            ])
            ->add('errormessage_required', TextType::class, [
                'label' => 'kuma_form.form.choice_page_part.errormessage_required.label',
                'required' => false,
            ])
            ->add('expanded', CheckboxType::class, [
                'label' => 'kuma_form.form.choice_page_part.expanded.label',
                'required' => false,
            ])
            ->add('multiple', CheckboxType::class, [
                'label' => 'kuma_form.form.choice_page_part.multiple.label',
                'required' => false,
            ])
            ->add('choices', TextareaType::class, [
                'label' => 'kuma_form.form.choice_page_part.choices.label',
                'required' => false,
            ])
            ->add('empty_value', TextType::class, [
                'label' => 'kuma_form.form.choice_page_part.empty_value.label',
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_choicepageparttype';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart']);
    }
}
