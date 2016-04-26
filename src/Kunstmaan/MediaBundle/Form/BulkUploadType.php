<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * BulkUploadType
 */
class BulkUploadType extends AbstractType
{

    /**
     * @var string
     */
    protected $accept;

    /**
     * contructor
     *
     * @param string $accept
     */
    public function __construct($accept = null)
    {
        $this->accept = $accept;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'files',
            FileType::class,
            array(
                'label' => 'media.form.bulk_upload.files.label',
                'required' => false,
                'attr' => array(
                    'accept' => $this->accept,
                    'multiple' => 'multiple',
                ),
                'data_class' => null
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_mediabundle_bulkupload';
    }
}
