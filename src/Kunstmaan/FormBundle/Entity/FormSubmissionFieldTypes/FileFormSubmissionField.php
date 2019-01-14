<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The ChoiceFormSubmissionField can be used to store files to a FormSubmission
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="kuma_file_form_submission_fields")
 */
class FileFormSubmissionField extends FormSubmissionField
{
    /**
     * The file name
     *
     * @ORM\Column(name="ffsf_value", type="string")
     */
    protected $fileName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=255)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * Non-persistent storage of upload file
     *
     * @Assert\File(maxSize="6000000")
     *
     * @var UploadedFile
     */
    public $file;

    /**
     * A string representation of the current value
     *
     * @return string
     */
    public function __toString()
    {
        if (!empty($this->url)) {
            return $this->url;
        }

        return !empty($this->fileName) ? $this->fileName : '';
    }

    /**
     * Checks if a file has been uploaded
     *
     * @return bool
     */
    public function isNull()
    {
        return null === $this->file && empty($this->fileName);
    }

    /**
     * Move the file to the given uploadDir and save the filename
     *
     * @param string $uploadDir
     */
    public function upload($uploadDir, $webDir)
    {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // sanitize filename for security
        $safeFileName = $this->getSafeFileName($this->file);

        $uuid = uniqid();
        $this->setUuid($uuid);

        // move takes the target directory and then the target filename to move to
        $this->file->move(sprintf('%s/%s', $uploadDir, $uuid), $safeFileName);

        // set the path property to the filename where you'ved saved the file
        $this->fileName = $safeFileName;

        // set the url to the uuid directory inside the web dir
        $this->setUrl(sprintf('%s%s/', $webDir, $uuid) . $safeFileName);

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * This function will be triggered if the form was successfully posted.
     *
     * @param Form                 $form        the Form
     * @param FormBuilderInterface $formBuilder the FormBuilder
     * @param Request              $request     the Request
     * @param ContainerInterface   $container   the Container
     */
    public function onValidPost(Form $form, FormBuilderInterface $formBuilder, Request $request, ContainerInterface $container)
    {
        $uploadDir = $container->getParameter('form_submission_rootdir');
        $webDir = $container->getParameter('form_submission_webdir');
        $this->upload($uploadDir, $webDir);
    }

    /**
     * Create a safe file name for the uploaded file, so that it can be saved safely on the disk.
     *
     * @return string
     */
    public function getSafeFileName()
    {
        $fileExtension = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $mimeTypeExtension = $this->file->guessExtension();
        $newExtension = !empty($mimeTypeExtension) ? $mimeTypeExtension : $fileExtension;

        $baseName = !empty($fileExtension) ? basename($this->file->getClientOriginalName(), $fileExtension) : $this->file->getClientOriginalName();
        $safeBaseName = Urlizer::urlize($baseName);

        return $safeBaseName . (!empty($newExtension) ? '.' . $newExtension : '');
    }

    /**
     * Set the filename for the uploaded file
     *
     * @param string $fileName
     *
     * @return FileFormSubmissionField
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Returns the filename of the uploaded file
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return FileFormSubmissionField
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return FileFormSubmissionField
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the template for this field
     *
     * @return string
     */
    public function getSubmissionTemplate()
    {
        return 'KunstmaanFormBundle:FileUploadPagePart:submission.html.twig';
    }

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return FileFormSubmissionType::class;
    }
}
