<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Kunstmaan\FormBundle\Validator\Constraints\AllowedUploadExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'kuma_file_form_submission_fields')]
class FileFormSubmissionField extends FormSubmissionField
{
    /**
     * The file name
     *
     * @ORM\Column(name="ffsf_value", type="string")
     */
    #[ORM\Column(name: 'ffsf_value', type: 'string')]
    protected $fileName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=255)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Column(name: 'uuid', type: 'string', unique: true, length: 255)]
    #[ORM\GeneratedValue('AUTO')]
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    #[ORM\Column(name: 'url', type: 'string', nullable: true)]
    protected $url;

    /**
     * Non-persistent storage of upload file
     *
     * @var UploadedFile
     */
    #[Assert\File(maxSize: '6000000')]
    #[AllowedUploadExtension]
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
     * @param string   $uploadDir
     * @param string   $webDir
     * @param string[] $allowedExtensions the allow-list of extensions the stored file may have; empty means no restriction
     */
    public function upload($uploadDir, $webDir, array $allowedExtensions = [])
    {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // sanitize filename for security
        $safeFileName = $this->getSafeFileName($allowedExtensions);

        // use a non-guessable directory name so the stored file location cannot be predicted
        $uuid = bin2hex(random_bytes(16));
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
        $allowedExtensions = $container->hasParameter('kunstmaan_form.file_upload.allowed_extensions')
            ? $container->getParameter('kunstmaan_form.file_upload.allowed_extensions')
            : [];
        $this->upload($uploadDir, $webDir, $allowedExtensions);
    }

    /**
     * Create a safe file name for the uploaded file, so that it can be saved safely on the disk.
     *
     * @param string[] $allowedExtensions the allow-list of extensions the stored file may have; empty means no restriction
     *
     * @return string
     */
    public function getSafeFileName(array $allowedExtensions = [])
    {
        $extension = $this->file->guessExtension();
        if (null === $extension) {
            throw new FileException('The type of the uploaded file could not be determined and is therefore not allowed.');
        }

        if ([] !== $allowedExtensions && !\in_array(strtolower($extension), array_map('strtolower', $allowedExtensions), true)) {
            throw new FileException(sprintf('The type of the uploaded file ("%s") is not allowed.', $extension));
        }

        $baseName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Transliterator::urlize($baseName);
        if ('' === $safeBaseName) {
            $safeBaseName = 'file';
        }

        return $safeBaseName . (!empty($extension) ? '.' . $extension : '');
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
        return '@KunstmaanForm/FileUploadPagePart/submission.html.twig';
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
