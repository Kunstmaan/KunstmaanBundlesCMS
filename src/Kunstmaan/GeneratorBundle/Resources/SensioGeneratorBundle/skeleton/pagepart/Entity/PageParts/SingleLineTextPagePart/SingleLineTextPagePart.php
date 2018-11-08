<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;

/**
 * {{ pagepart }}
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
class {{ pagepart }} extends AbstractFormPagePart
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
     * If set the entered value will be matched with this regular expression
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $regex;

    /**
     * If a regular expression is set and it doesn't match with the given value, this error message will be shown
     *
     * @ORM\Column(type="string", name="error_message_regex", nullable=true)
     */
    protected $errorMessageRegex;

    /**
     * Internal name that can be used with form submission subscribers.
     *
     * @ORM\Column(type="string", name="internal_name", nullable=true)
     */
    protected $internalName;

    /**
     * Sets the required valud of this page part
     *
     * @param bool $required
     *
     * @return SingleLineTextPagePart
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
     * @return SingleLineTextPagePart
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
     * Set the regular expression to match the entered value against
     *
     * @param string $regex
     *
     * @return SingleLineTextPagePart
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Get the current regular expression
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Set the error message which will be shown when the entered value doesn't match the regular expression
     *
     * @param string $errorMessageRegex
     *
     * @return SingleLineTextPagePart
     */
    public function setErrorMessageRegex($errorMessageRegex)
    {
        $this->errorMessageRegex = $errorMessageRegex;

        return $this;
    }

    /**
     * Get the current error message which will be shown when the entered value doesn't match the regular expression
     *
     * @return string
     */
    public function getErrorMessageRegex()
    {
        return $this->errorMessageRegex;
    }

    /**
     * @param string $internalName
     *
     * @return self
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Returns the frontend view
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
    }

    /**
     * Modify the form with the fields of the current page part
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     * @param int                  $sequence    The sequence of the form field
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence)
    {
        $sfsf = new StringFormSubmissionField();
        $sfsf->setFieldName('field_' . $this->getUniqueId());
        $sfsf->setLabel($this->getLabel());
        $sfsf->setInternalName($this->getInternalName());
        $sfsf->setSequence($sequence);

        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $sfsf;

        $constraints = array();
        if ($this->getRequired()) {
            $options = array();
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }
        if ($this->getRegex()) {
            $options = array('pattern' => $this->getRegex());
            if (!empty($this->errorMessageRegex)) {
                $options['message'] = $this->errorMessageRegex;
            }
            $constraints[] = new Regex($options);
        }

        $formBuilder->add('formwidget_' . $this->getUniqueId(),
            StringFormSubmissionType::class,
            array(
                'label'       => $this->getLabel(),
                'constraints' => $constraints,
                'required'    => $this->getRequired()
            )
        );
        $formBuilder->setData($data);

        $fields->append($sfsf);
    }

    /**
     * Returns the default backend form type for this page part
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }
}
