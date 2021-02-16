<?php

namespace Kunstmaan\TranslatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TranslationsFileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'required' => true,
            'label' => 'kuma_translator.form.upload_file_choose',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('force', CheckboxType::class, [
            'required' => false,
            'label' => 'kuma_translator.form.force_checkbox',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'translation_file_upload';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
