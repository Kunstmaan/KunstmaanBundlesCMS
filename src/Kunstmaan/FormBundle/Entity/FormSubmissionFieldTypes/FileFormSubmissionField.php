<?php
namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionField;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * FileFormSubmissionField
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="form_fileformsubmissionfield")
 */
class FileFormSubmissionField extends FormSubmissionField
{

	/**
	 * The file name
	 * @ORM\Column(name="ffsf_value", type="string")
	 */
	protected $file_name;

	/**
	 * non-persistent storage of upload file
	 * @Assert\File(maxSize="6000000")
	 */
	public $file;

	public function __toString()
	{
		return !empty($this->file_name) ? $this->file_name : "";
	}

	public function isNull()
	{
		return null === $this->file && empty($this->file_name);
	}

	public function upload($uploadDir)
	{
		// the file property can be empty if the field is not required
		if (null === $this->file) {
			return;
		}

		// sanitize filename for security
		$safeFileName = $this->getSafeFileName($this->file);

		// move takes the target directory and then the target filename to move to
		$this->file->move($uploadDir, $safeFileName);

		// set the path property to the filename where you'ved saved the file
		$this->file_name = $safeFileName;

		// clean up the file property as you won't need it anymore
		$this->file = null;
	}

	public function onValidPost($form, $formbuilder, $request, $container)
	{
		// do nothing by default
		$uploadDir = $container->getParameter('formsubmission_rootdir');
		$this->upload($uploadDir);
	}

	public function getSafeFileName($file)
	{
		$fileExtension = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
		$mimeTypeExtension = $file->guessExtension();
		$newExtension = !empty($mimeTypeExtension) ? $mimeTypeExtension : $fileExtension;

		$baseName = !empty($fileExtension) ? basename($this->file->getClientOriginalName(), $fileExtension) : $this->file->getClientOriginalName();
		$safeBaseName = Urlizer::urlize($baseName);

		return $safeBaseName.(!empty($newExtension) ? '.'.$newExtension : '');
	}

	public function setFileName($file_name)
	{
		$this->file_name = $file_name;
	}

	public function getFileName()
	{
		return $this->file_name;
	}

	public function getSubmissionTemplate()
	{
		return "KunstmaanFormBundle:FileUploadPagePart:submission.html.twig";
	}

}