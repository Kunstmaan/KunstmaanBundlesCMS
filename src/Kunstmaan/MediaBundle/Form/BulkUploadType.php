<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BulkUploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'files',
            FileType::class,
            [
                'label' => 'media.form.bulk_upload.files.label',
                'required' => false,
                'attr' => [
                    'accept' => $options['accept'],
                    'multiple' => 'multiple',
                ],
                'data_class' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('accept', null);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_mediabundle_bulkupload';
    }
}
