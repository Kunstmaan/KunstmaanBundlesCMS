<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormBuilderInterface;
use ArrayObject;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\TextFormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;
use Kunstmaan\FormBundle\Form\MultiLineTextPagePartAdminType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\Mapping as ORM;

/**
 * Multi line text pagepart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_multi_line_text_page_parts")
 */
class MultiLineTextPagePart extends AbstractFormPagePart
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $regex;

    /**
     * @ORM\Column(type="string", name="error_message_regex", nullable=true)
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
        return "KunstmaanFormBundle:MultiLineTextPagePart:view.html.twig";
    }

    /**
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields)
    {
        $mfsf = new TextFormSubmissionField();
        $mfsf->setFieldName("field_" . $this->getUniqueId());
        $mfsf->setLabel($this->getLabel());
        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $mfsf;
        $label = $this->getLabel();
        if ($this->getRequired()) {
            $label = $label . ' *';
        }
        $formBuilder->add('formwidget_' . $this->getUniqueId(), new TextFormSubmissionType($label));
        $formBuilder->setData($data);
        if ($this->getRequired()) {
            $formBuilder->addValidator(
                new FormValidator($mfsf, $this,
                    function (FormInterface $form, TextFormSubmissionField $sfsf, MultiLineTextPagePart $thiss) {
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
                        new FormValidator($mfsf, $this,
                            function (FormInterface $form, TextFormSubmissionField $sfsf, MultiLineTextPagePart $thiss) {
                                        $value = $sfsf->getValue();
                                        if (!is_null($value) && is_string($value) && !preg_match('/' . $thiss->getRegex() . '/', $value)) {
                                            $v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
                                            $v->addError(new FormError($thiss->getErrorMessageRegex()));
                                        }
                            }));
        }
        $fields[] = $mfsf;
    }

    /**
     * @return MultiLineTextPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new MultiLineTextPagePartAdminType();
    }

}
