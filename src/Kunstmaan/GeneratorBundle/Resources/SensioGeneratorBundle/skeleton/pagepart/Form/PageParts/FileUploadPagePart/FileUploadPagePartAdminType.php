<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true,
                'label' => 'kuma_form.form.file_upload_page_part.label.label',
            ])
            ->add('required', CheckboxType::class, [
                'required' => false,
                'label' => 'kuma_form.form.file_upload_page_part.required.label',
            ])
            ->add('errormessage_required', TextType::class, [
                'required' => false,
                'label' => 'kuma_form.form.file_upload_page_part.errormessage_required.label',
            ])
            ->add('internalName', TextType::class, [
                'required' => false,
                'label' => 'kuma_form.form.form_page_part.internal_name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart }}::class,
        ]);
    }
}
