<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * If set to true, radio buttons or checkboxes will be rendered (depending on the multiple value). If false,
     * a select element will be rendered.
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
    protected $expanded = false;

    /**
     * If true, the user will be able to select multiple options (as opposed to choosing just one option).
     * Depending on the value of the expanded option, this will render either a select tag or checkboxes
     * if true and a select tag or radio buttons if false. The returned value will be an array.
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
    protected $multiple = false;

    /**
     * The choices that should be used by this field. The choices can be entered separated by a new line.
     *
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="text", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(type: 'text', nullable: true)]
{% endif %}
    protected $choices;

    /**
     * This option determines whether or not a special "empty" option (e.g. "Choose an option")
     * will appear at the top of a select widget. This option only applies if both the expanded and
     * multiple options are set to false.
     *
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", name="empty_value", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'empty_value', type: 'string', nullable: true)]
{% endif %}
    protected $emptyValue;

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

    public function getDefaultView(): string
    {
        return 'PageParts/{{ pagepart }}/view.html.twig';
    }

    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence): void
    {
        $choices = explode("\n", $this->getChoices());
        $choices = array_map('trim', $choices);

        $cfsf = new ChoiceFormSubmissionField();
        $cfsf->setFieldName('field_'.$this->getUniqueId());
        $cfsf->setLabel($this->getLabel());
        $cfsf->setChoices($choices);
        $cfsf->setRequired($this->required);
        $cfsf->setSequence($sequence);
        $cfsf->setInternalName($this->getInternalName());

        $data = $formBuilder->getData();
        $data['formwidget_'.$this->getUniqueId()] = $cfsf;
        $constraints = [];
        if ($this->getRequired()) {
            $options = [];
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }

        $formBuilder->add('formwidget_'.$this->getUniqueId(), ChoiceFormSubmissionType::class, [
            'label' => $this->getLabel(),
            'required' => $this->getRequired(),
            'expanded' => $this->getExpanded(),
            'multiple' => $this->getMultiple(),
            'choices' => $choices,
            'placeholder' => $this->getEmptyValue(),
            'value_constraints' => $constraints,
        ]);
        $formBuilder->setData($data);

        $fields->append($cfsf);
    }

    public function getDefaultAdminType(): string
    {
        return {{ adminType }}::class;
    }

    public function setExpanded(bool $expanded): ChoicePagePart
    {
        $this->expanded = $expanded;

        return $this;
    }

    public function getExpanded(): bool
    {
        return $this->expanded;
    }

    public function setMultiple(bool $multiple): ChoicePagePart
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    public function setChoices(?string $choices): ChoicePagePart
    {
        $this->choices = $choices;

        return $this;
    }

    public function getChoices(): string
    {
        return $this->choices ?? '';
    }

    public function setEmptyValue(?string $emptyValue): ChoicePagePart
    {
        $this->emptyValue = $emptyValue;

        return $this;
    }

    public function getEmptyValue(): ?string
    {
        return $this->emptyValue;
    }

    public function setRequired(bool $required): ChoicePagePart
    {
        $this->required = $required;

        return $this;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function setErrorMessageRequired(?string $errorMessageRequired): ChoicePagePart
    {
        $this->errorMessageRequired = $errorMessageRequired;

        return $this;
    }

    public function getErrorMessageRequired(): ?string
    {
        return $this->errorMessageRequired;
    }

    public function setInternalName(?string $internalName): ChoicePagePart
    {
        $this->internalName = $internalName;

        return $this;
    }

    public function getInternalName(): ?string
    {
        return $this->internalName;
    }
}
