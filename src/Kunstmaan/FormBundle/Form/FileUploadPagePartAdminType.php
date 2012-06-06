<?php
namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileUploadPagePartAdminType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('label', null, array('required' => true))
			->add('required', 'checkbox', array('required' => false))
			->add('errormessage_required', 'text', array('required' => false));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'kunstmaan_formbundle_fileuploadpageparttype';
	}
}
