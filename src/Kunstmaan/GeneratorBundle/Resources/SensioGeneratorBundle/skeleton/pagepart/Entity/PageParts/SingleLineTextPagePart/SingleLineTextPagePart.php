<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}{{ underscoreName }}s')]
{% else %}
/**
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
{% endif %}
class {{ pagepart }} extends AbstractFormPagePart
{
    /**
     * If set to true, you are obligated to fill in this page part.
     *
     * @var bool
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="boolean", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(type: 'boolean', nullable: true)]
{% endif %}
    protected $required = false;

    /**
     * Error message shows when the page part is required and nothing is filled in.
     *
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", name="error_message_required", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'error_message_required', type: 'string', nullable: true)]
{% endif %}
    protected $errorMessageRequired;

    /**
     * If set the entered value will be matched with this regular expression.
     *
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(type: 'string', nullable: true)]
{% endif %}
    protected $regex;

    /**
     * If a regular expression is set and it doesn't match with the given value, this error message will be shown.
     *
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", name="error_message_regex", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'error_message_regex', type: 'string', nullable: true)]
{% endif %}
    protected $errorMessageRegex;

    /**
     * Internal name that can be used with form submission subscribers.
     *
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", name="internal_name", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'internal_name', type: 'string', nullable: true)]
{% endif %}
    protected $internalName;

    public function setRequired(bool $required): SingleLineTextPagePart
    {
        $this->required = $required;

        return $this;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function setErrorMessageRequired(?string $errorMessageRequired): SingleLineTextPagePart
    {
        $this->errorMessageRequired = $errorMessageRequired;

        return $this;
    }

    public function getErrorMessageRequired(): ?string
    {
        return $this->errorMessageRequired;
    }

    public function setRegex(?string $regex): SingleLineTextPagePart
    {
        $this->regex = $regex;

        return $this;
    }

    public function getRegex(): ?string
    {
        return $this->regex;
    }

    public function setErrorMessageRegex(?string $errorMessageRegex): SingleLineTextPagePart
    {
        $this->errorMessageRegex = $errorMessageRegex;

        return $this;
    }

    public function getErrorMessageRegex(): ?string
    {
        return $this->errorMessageRegex;
    }

    public function setInternalName(?string $internalName): SingleLineTextPagePart
    {
        $this->internalName = $internalName;

        return $this;
    }

    public function getInternalName(): ?string
    {
        return $this->internalName;
    }

    public function getDefaultView(): string
    {
        return 'PageParts/{{ pagepart }}/view.html.twig';
    }

    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence): void
    {
        $sfsf = new StringFormSubmissionField();
        $sfsf->setFieldName('field_'.$this->getUniqueId());
        $sfsf->setLabel($this->getLabel());
        $sfsf->setInternalName($this->getInternalName());
        $sfsf->setSequence($sequence);

        $data = $formBuilder->getData();
        $data['formwidget_'.$this->getUniqueId()] = $sfsf;

        $constraints = [];
        if ($this->getRequired()) {
            $options = [];
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }
        if ($this->getRegex()) {
            $options = ['pattern' => $this->getRegex()];
            if (!empty($this->errorMessageRegex)) {
                $options['message'] = $this->errorMessageRegex;
            }
            $constraints[] = new Regex($options);
        }

        $formBuilder->add('formwidget_'.$this->getUniqueId(), StringFormSubmissionType::class, [
            'label' => $this->getLabel(),
            'value_constraints' => $constraints,
            'required' => $this->getRequired(),
        ]);
        $formBuilder->setData($data);

        $fields->append($sfsf);
    }

    public function getDefaultAdminType(): string
    {
        return {{ adminType }}::class;
    }
}
