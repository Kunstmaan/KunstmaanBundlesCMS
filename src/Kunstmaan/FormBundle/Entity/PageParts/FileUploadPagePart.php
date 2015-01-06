<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use ArrayObject;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Kunstmaan\FormBundle\Form\FileUploadPagePartAdminType;

/**
 * The file upload page part can be used to create forms with the possibility to upload files
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="kuma_file_upload_page_parts")
 */
class FileUploadPagePart extends AbstractFormPagePart
{

    /**
     * If set to true, you are obligated to fill in this page part
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $required = false;

    /**
     * Error message shows when the page part is required and nothing is filled in
     *
     * @ORM\Column(type="string", name="error_message_required", nullable=true)
     */
    protected $errorMessageRequired;

    /**
     * Modify the form with the fields of the current page part
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields)
    {
        $ffsf = new FileFormSubmissionField();
        $ffsf->setFieldName("field_" . $this->getUniqueId());
        $ffsf->setLabel($this->getLabel());
        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $ffsf;

        $constraints = array();
        if ($this->getRequired()) {
            $options = array();
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }

        $formBuilder->add(
            'formwidget_' . $this->getUniqueId(),
            new FileFormSubmissionType(),
            array(
                'label'       => $this->getLabel(),
                'constraints' => $constraints,
                'required'    => $this->getRequired()
            )
        );
        $formBuilder->setData($data);

        $fields[] = $ffsf;
    }

    /**
     * Sets the required valud of this page part
     *
     * @param bool $required
     *
     * @return FileUploadPagePart
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Check if the page part is required
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Sets the message shown when the page part is required and no value was entered
     *
     * @param string $errorMessageRequired
     *
     * @return FileUploadPagePart
     */
    public function setErrorMessageRequired($errorMessageRequired)
    {
        $this->errorMessageRequired = $errorMessageRequired;

        return $this;
    }

    /**
     * Get the error message that will be shown when the page part is required and no value was entered
     *
     * @return string
     */
    public function getErrorMessageRequired()
    {
        return $this->errorMessageRequired;
    }

    /**
     * Returns the view used in the frontend
     *
     * @return mixed
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:FileUploadPagePart:view.html.twig";
    }

    /**
     * Returns the default backend form type for this page part
     *
     * @return FileUploadPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new FileUploadPagePartAdminType();
    }

}
