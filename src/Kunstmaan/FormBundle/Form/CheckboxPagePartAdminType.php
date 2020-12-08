<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class represents the type for the CheckboxPagePart
 */
class CheckboxPagePartAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'kuma_form.form.checkbox_page_part.label.label',
                'required' => true,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'kuma_form.form.checkbox_page_part.required.label',
                'required' => false,
            ])
            ->add('errormessage_required', TextType::class, [
                'label' => 'kuma_form.form.checkbox_page_part.errormessage_required.label',
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_checkboxpageparttype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Kunstmaan\FormBundle\Entity\PageParts\CheckboxPagePart']);
    }
}
