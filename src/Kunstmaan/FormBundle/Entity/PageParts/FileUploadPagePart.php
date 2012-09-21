<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use ArrayObject;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Kunstmaan\FormBundle\Form\FileUploadPagePartAdminType;

/**
 * The file upload page part can be used to create forms with the possibility to upload files.
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="kuma_file_upload_page_parts")
 */
class FileUploadPagePart extends AbstractFormPagePart
{

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
            $formBuilder->addValidator(
                new FormValidator($ffsf, $this,
                    function(FormInterface $form, FileFormSubmissionField $ffsf, FileUploadPagePart $thiss) {
                        if ($ffsf->isNull()) {
                            $errormsg = $thiss->getErrorMessageRequired();
                            $v = $form->get('formwidget_' . $thiss->getUniqueId())->get('file');
                            $formError = new FormError(empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg);
                            $v->addError($formError);
                        }
                    }
                )
            );
        }

        $fields[] = $ffsf;
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
