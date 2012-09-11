<?php

namespace Kunstmaan\FormBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * TextFormSubmissionType
 */
class TextFormSubmissionType extends AbstractType
{
    private $label;

    /**
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('value', 'textarea',
                    array('label' => $this->label, 'attr' => array('rows' => '6')));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\TextFormSubmissionField',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_stringformsubmissiontype';
    }
}
