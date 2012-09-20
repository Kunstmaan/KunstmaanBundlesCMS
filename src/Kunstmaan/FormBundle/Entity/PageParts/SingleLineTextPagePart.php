<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use ArrayObject;
use Symfony\Component\Form\FormBuilderInterface;


use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\SingleLineTextPagePartAdminType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\Mapping as ORM;

/**
 * A single line pagepart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_single_line_text_page_parts")
 */
class SingleLineTextPagePart extends AbstractFormPagePart
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $regex;

    /**
     * @ORM\Column(type="string", name="error_essage_regex", nullable=true)
     */
    protected $errorMessageRegex;

    /**
     * @param string $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @param string $errorMessageRegex
     */
    public function setErrorMessageRegex($errorMessageRegex)
    {
        $this->errorMessageRegex = $errorMessageRegex;
    }

    /**
     * @return string
     */
    public function getErrorMessageRegex()
    {
        return $this->errorMessageRegex;
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:SingleLineTextPagePart:view.html.twig";
    }

    /**
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields)
    {
        $sfsf = new StringFormSubmissionField();
        $sfsf->setFieldName("field_" . $this->getUniqueId());
        $sfsf->setLabel($this->getLabel());
        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $sfsf;
        $label = $this->getLabel();
        if ($this->getRequired()) {
            $label = $label . ' *';
        }
        $formBuilder->add('formwidget_' . $this->getUniqueId(), new StringFormSubmissionType($label));
        $formBuilder->setData($data);
        if ($this->getRequired()) {
            $formBuilder->addValidator(
                new FormValidator($sfsf, $this,
                    function (FormInterface $form, StringFormSubmissionField $sfsf, SingleLineTextPagePart $thiss) {
                        $value = $sfsf->getValue();
                        if (is_null($value) || !is_string($value) || empty($value)) {
                            $errormsg = $thiss->getErrorMessageRequired();
                            $v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
                            $v->addError(new FormError(empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg));
                        }

                    }
                )
            );
        }
        if ($this->getRegex()) {
            $formBuilder
                    ->addValidator(
                        new FormValidator($sfsf, $this,
                            function (FormInterface $form, StringFormSubmissionField $sfsf, SingleLineTextPagePart $thiss) {
                                        $value = $sfsf->getValue();
                                        if (!is_null($value) && is_string($value) && !preg_match('/' . $thiss->getRegex() . '/', $value)) {
                                            $v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
                                            $v->addError(new FormError($thiss->getErrorMessageRegex()));
                                        }
                            }));
        }
        $fields[] = $sfsf;
    }

    /**
     * @return SingleLineTextPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new SingleLineTextPagePartAdminType();
    }

}
