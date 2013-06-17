<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This class represents the type for the ChoiceFormSubmissionField
 */
class ChoiceFormSubmissionType extends AbstractType
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $expanded;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var array
     */
    private $choices;

    /**
     * @var string
     */
    private $emptyValue;

    /**
     * @param string $label      The label
     * @param        $required
     * @param bool   $expanded   Expanded or not
     * @param bool   $multiple   Multiple or not
     * @param array  $choices    The choices array
     * @param array  $emptyValue The empty value
     */
    public function __construct($label, $required, $expanded, $multiple, array $choices, $emptyValue = null)
    {
        $this->label = $label;
        $this->required = $required;
        $this->expanded = $expanded;
        $this->multiple = $multiple;
        $this->choices = $choices;
        $this->emptyValue = $emptyValue;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'choice', array(
            'label' => $this->label,
            'required' => $this->required,
            'expanded' => $this->expanded,
            'multiple' => $this->multiple,
            'choices' => $this->choices,
            'empty_value' => $this->emptyValue,
            'empty_data' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_choiceformsubmissiontype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField',
        ));
    }
}
