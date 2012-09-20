<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * The type for the StringFormSubmissionField
 */
class StringFormSubmissionType extends AbstractType
{

    /**
     * @var string
     */
    private $label;

    /**
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'text', array(
            'label' => $this->label
        ));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField',
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_stringformsubmissiontype';
    }
}
