<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This class represents the type for the SubleLineTextPagePart
 */
class SingleLineTextPagePartAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, array(
                'required' => false,
                'label' => 'kuma_form.form.single_line_text_page_part.label.label',
            ))
            ->add('required', CheckboxType::class, array(
                'required' => false,
                'label' => 'kuma_form.form.single_line_text_page_part.required.label',
            ))
            ->add('errormessage_required', TextType::class, array(
                'required' => false,
                'label' => 'kuma_form.form.single_line_text_page_part.errormessage_required.label',
            ))
            ->add('regex', TextType::class, array(
                'required' => false,
                'label' => 'kuma_form.form.single_line_text_page_part.regex.label',
            ))
            ->add('errormessage_regex', TextType::class, array(
                'required' => false,
                'label' => 'kuma_form.form.single_line_text_page_part.errormessage_regex.label',
            ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_singlelinetextpageparttype';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart'));
    }
}
