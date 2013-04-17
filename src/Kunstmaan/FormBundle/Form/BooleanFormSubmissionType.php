<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The type for the BooleanFormSubmissionField
 */
class BooleanFormSubmissionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldOptions = array();
        if (isset($options['label'])) {
            $fieldOptions['label'] = $options['label'];
        }
        if (isset($options['constraints'])) {
            $fieldOptions['constraints'] = $options['constraints'];
        }
        $builder->add('value', 'checkbox', $fieldOptions);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField',
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_booleanformsubmissiontype';
    }
}