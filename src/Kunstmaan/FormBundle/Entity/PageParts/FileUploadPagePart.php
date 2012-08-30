<?php
namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\FormBundle\Form\FileUploadPagePartAdminType;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

/**
 * File upload form pagepart
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="form_fileuploadpagepart")
 */
class FileUploadPagePart extends AbstractFormPagePart
{

	/**
	 * adapt the form here
	 * @param FormBuilder $formBuilder The formbuilder
	 * @param array       &$fields     The fields
	 */
	public function adaptForm(FormBuilderInterface $formBuilder, &$fields)
	{
		$ffsf = new FileFormSubmissionField();
		$ffsf->setFieldName("field_" . $this->getUniqueId());
		$ffsf->setLabel($this->getLabel());
		$data = $formBuilder->getData();
		$data['formwidget_' . $this->getUniqueId()] = $ffsf;
		$label = $this->getLabel();
		if ($this->getRequired()) {
			$label = $label . ' *';
		}

		$formBuilder->add('formwidget_' . $this->getUniqueId(), new FileFormSubmissionType($label, $this->getRequired()));
		$formBuilder->setData($data);

		if ($this->getRequired()) {
			$formBuilder->addValidator(
				new FormValidator($ffsf, $this,	function(FormInterface $form, $ffsf, $thiss)
				{
					if ($ffsf->isNull()) {
						$errormsg = $thiss->getErrormessageRequired();
						$v = $form->get('formwidget_' . $thiss->getUniqueId())->get('file');
						$v->addError(new FormError( empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg));

					}
				}
			));
		}

		$fields[] = $ffsf;
	}

	public function onPost($form, $formbuilder, $request, $container)
	{
		// do nothing by default

		$ffsf = $formbuilder->get('formwidget_' . $this->getUniqueId());
		$ffsf->upload();

	}

	/**
	 * Returns the view used in the frontend
	 * @return mixed
	 */
	public function getDefaultView()
	{
		return "KunstmaanFormBundle:FileUploadPagePart:view.html.twig";
	}

	/**
	 * @return AbstractType
	 */
	public function getDefaultAdminType()
	{
		return new FileUploadPagePartAdminType();
	}

}