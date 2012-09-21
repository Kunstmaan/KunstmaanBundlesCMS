<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This class represents the type for the file FileFormSubmissionField
 */
class FileFormSubmissionType extends AbstractType
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @param string $label    The label
     * @param bool   $required Is required
     */
    public function __construct($label, $required)
    {
        $this->label = $label;
        $this->required = $required;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options An array with options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
            'label' => $this->label,
            'required' => $this->required
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_fileformsubmissiontype';
    }
}
