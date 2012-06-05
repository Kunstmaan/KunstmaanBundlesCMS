<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\TextFormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;
use Kunstmaan\FormBundle\Form\MultiLineTextPagePartAdminType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormBuilder;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 * Multi line text pagepart
 * 
 * @ORM\Entity
 * @ORM\Table(name="form_multilinetextpagepart")
 */
class MultiLineTextPagePart extends AbstractFormPagePart
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $regex;

    /**
     * @ORM\Column(type="string", name="errormessage_regex", nullable=true)
     */
    protected $errormessageRegex;

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
     * @param string $errormessageRegex
     */
    public function setErrormessageRegex($errormessageRegex)
    {
        $this->errormessageRegex = $errormessageRegex;
    }

    /**
     * @return string
     */
    public function getErrormessageRegex()
    {
        return $this->errormessageRegex;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:MultiLineTextPagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function adaptForm(FormBuilder $formBuilder, &$fields)
    {
        $sfsf = new TextFormSubmissionField();
        $sfsf->setFieldName("field_" . $this->getUniqueId());
        $sfsf->setLabel($this->getLabel());
        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $sfsf;
        $label = $this->getLabel();
        if ($this->getRequired()) {
            $label = $label . ' *';
        }
        $formBuilder->add('formwidget_' . $this->getUniqueId(), new TextFormSubmissionType($label));
        $formBuilder->setData($data);
        if ($this->getRequired()) {
            $formBuilder->addValidator(
				new FormValidator($sfsf, $this,
					function (FormInterface $form, $sfsf, $thiss)
					{
						$value = $sfsf->getValue();
						if ($value != null && !is_string($value)) {
							$v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
							$v->addError(new FormError($thiss->getErrormessageRequired()));
						}
					}
			));
        }
        if ($this->getRegex()) {
            $formBuilder
                    ->addValidator(
                        new FormValidator($sfsf, $this,
                            function (FormInterface $form, $sfsf, $thiss)
                                    {
                                        $value = $sfsf->getValue();
                                        if ($value != null && is_string($value) && !preg_match('/' . $thiss->getRegex() . '/', $value)) {
                                            $v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
                                            $v->addError(new FormError($thiss->getErrormessageRegex()));
                                        }
                            }));
        }
        $fields[] = $sfsf;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new MultiLineTextPagePartAdminType();
    }

}
