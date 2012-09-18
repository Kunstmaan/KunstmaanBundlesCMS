<?php
namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

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
            ->add('label', null, array('required' => true))
            ->add('required', 'checkbox', array('required' => false))
            ->add('errormessage_required', 'text', array('required' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_fileuploadpageparttype';
    }
}
