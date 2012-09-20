<?php
namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionField;

use Gedmo\Sluggable\Util\Urlizer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a file form submission field
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="kuma_file_form_submission_fields")
 */
class FileFormSubmissionField extends FormSubmissionField
{

    /**
     * The file name
     * @ORM\Column(name="ffsf_value", type="string")
     */
    protected $fileName;

    /**
     * non-persistent storage of upload file
     * @Assert\File(maxSize="6000000")
     * @var $file UploadedFile
     */
    public $file;

    /**
     * @return string
     */
    public function __toString()
    {
        return !empty($this->fileName) ? $this->fileName : "";
    }

    /**
     * @return bool
     */
    public function isNull()
    {
        return null === $this->file && empty($this->fileName);
    }

    /**
     * @param string $uploadDir
     */
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
        $this->fileName = $safeFileName;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @param Form                 $form        the Form
     * @param FormBuilderInterface $formBuilder the FormBuilder
     * @param Request              $request     the Request
     * @param ContainerInterface   $container   the Container
     */
    public function onValidPost(Form $form, FormBuilderInterface $formBuilder, Request $request, ContainerInterface $container)
    {
        $uploadDir = $container->getParameter('form_submission_rootdir');
        $this->upload($uploadDir);
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function getSafeFileName(UploadedFile $file)
    {
        $fileExtension = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $mimeTypeExtension = $file->guessExtension();
        $newExtension = !empty($mimeTypeExtension) ? $mimeTypeExtension : $fileExtension;

        $baseName = !empty($fileExtension) ? basename($this->file->getClientOriginalName(), $fileExtension) : $this->file->getClientOriginalName();
        $safeBaseName = Urlizer::urlize($baseName);

        return $safeBaseName . (!empty($newExtension) ? '.' . $newExtension : '');
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getSubmissionTemplate()
    {
        return "KunstmaanFormBundle:FileUploadPagePart:submission.html.twig";
    }

}
