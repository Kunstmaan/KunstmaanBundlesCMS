<?php

namespace Kunstmaan\FormBundle\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TextFormSubmissionType extends AbstractType
{
	private $label;

	public function __construct($label) {
		$this->label = $label;
	}


    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('value', 'textarea', array('data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField', 'label' => $this->label, 'attr' => array( 'rows' => '6' )))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_formbundle_stringformsubmissiontype';
    }
}

?>