<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use ArrayObject;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

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
        $label = $this->getLabel();
        if ($this->getRequired()) {
            $label = $label . ' *';
        }

        $formBuilder->add('formwidget_' . $this->getUniqueId(), new FileFormSubmissionType($label, $this->getRequired()));
        $formBuilder->setData($data);

        if ($this->getRequired()) {
            $formBuilder->addEventListener(FormEvents::POST_BIND, function(FormEvent $formEvent) use ($ffsf, $this) {
                $form = $formEvent->getForm();

                if ($ffsf->isNull()) {
                    $errormsg = $this->getErrorMessageRequired();
                    $v = $form->get('formwidget_' . $this->getUniqueId())->get('file');
                    $formError = new FormError(empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg);
                    $v->addError($formError);
                }
            });
        }

        $fields[] = $ffsf;
    }

    /**
     * Sets the required valud of this page part
     *
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
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
     */
    public function setErrorMessageRequired($errorMessageRequired)
    {
        $this->errorMessageRequired = $errorMessageRequired;
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
