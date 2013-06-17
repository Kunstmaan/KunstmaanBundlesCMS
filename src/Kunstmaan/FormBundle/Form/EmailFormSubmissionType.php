<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The type for the EmailFormSubmissionField
 */
class EmailFormSubmissionType extends AbstractType
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
        $builder->add('value', 'email', $fieldOptions);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_emailformsubmissiontype';
    }
}