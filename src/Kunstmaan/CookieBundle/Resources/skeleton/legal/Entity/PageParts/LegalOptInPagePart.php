<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;
use Kunstmaan\FormBundle\Form\BooleanFormSubmissionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

use {{ namespace }}\Form\PageParts\LegalOptInPagePartAdminType;

/**
 * The opt in pagepart creates a checkbox with a direct link to the privacy policy node.
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}legal_opt_in_page_parts")
 */
class LegalOptInPagePart extends AbstractFormPagePart
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
     * Sets the required valud of this page part
     *
     * @param bool $required
     *
     * @return CheckboxPagePart
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
     * @return CheckboxPagePart
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
     * Modify the form with the fields of the current page part
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param \ArrayObject          $fields      The fields
     * @param int                  $sequence    The sequence of the form field
     */
    public function adaptForm(FormBuilderInterface $formBuilder, \ArrayObject $fields, $sequence)
    {
        $bfsf = new BooleanFormSubmissionField();
        $bfsf->setFieldName('field_'.$this->getUniqueId());
        $bfsf->setLabel('Legal opt-in checked');
        $bfsf->setSequence($sequence);

        $data = $formBuilder->getData();
        $data['formwidget_'.$this->getUniqueId()] = $bfsf;
        $constraints = [];
        if ($this->getRequired()) {
            $options = [];
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }
        $formBuilder->add(
            'formwidget_'.$this->getUniqueId(),
            BooleanFormSubmissionType::class,
            [
                'label' => $this->getLabel(),
                'constraints' => $constraints,
                'required' => $this->getRequired(),
            ]
        );
        $formBuilder->setData($data);

        $fields->append($bfsf);
    }

    /**
     * Returns the frontend view
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts{% if not isV4 %}:{% else %}/{% endif %}LegalOptInPagePart/view.html.twig';
    }

    /**
     * Returns the default backend form type for this page part
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalOptInPagePartAdminType::class;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return 'legal_optin';
    }
}
