<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class represents the type for the MultiLineTextPagePart
 */
class MultiLineTextPagePartAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true,
                'label' => 'kuma_form.form.multi_line_text_page_part.label.label',
            ])
            ->add('required', CheckboxType::class, [
                'required' => false,
                'label' => 'kuma_form.form.multi_line_text_page_part.required.label',
            ])
            ->add('errorMessageRequired', TextType::class, [
                'required' => false,
                'label' => 'kuma_form.form.multi_line_text_page_part.errormessage_required.label',
            ])
            ->add('regex', TextType::class, [
                'required' => false,
                'label' => 'kuma_form.form.multi_line_text_page_part.regex.label',
            ])
            ->add('errorMessageRegex', TextType::class, [
                'required' => false,
                'label' => 'kuma_form.form.multi_line_text_page_part.errormessage_regex.label',
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_singlelinetextpageparttype';
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart']);
    }
}
