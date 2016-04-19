<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This class represents the type for the file FileUploadPagePart
 */
class FileUploadPagePartAdminType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, array(
                'required' => true,
                'label' => 'kuma_form.form.file_upload_page_part.label.label',
            ))
            ->add('required', CheckboxType::class, array(
                'required' => false,
                'label' => 'kuma_form.form.file_upload_page_part.required.label',
            ))
            ->add('errormessage_required', TextType::class, array(
                'required' => false,
                'label' => 'kuma_form.form.file_upload_page_part.errormessage_required.label',
            ))
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_formbundle_fileuploadpageparttype';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart'));
    }
}
