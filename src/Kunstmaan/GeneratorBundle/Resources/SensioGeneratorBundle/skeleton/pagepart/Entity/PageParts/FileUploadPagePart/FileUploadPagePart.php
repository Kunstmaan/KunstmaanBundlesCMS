<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
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

    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence): void
    {
        $ffsf = new FileFormSubmissionField();
        $ffsf->setFieldName('field_'.$this->getUniqueId());
        $ffsf->setLabel($this->getLabel());
        $ffsf->setSequence($sequence);
        $ffsf->setInternalName($this->getInternalName());

        $data = $formBuilder->getData();
        $data['formwidget_'.$this->getUniqueId()] = $ffsf;

        $constraints = [];
        if ($this->getRequired()) {
            $options = [];
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }

        $formBuilder->add('formwidget_'.$this->getUniqueId(), FileFormSubmissionType::class, [
            'label' => $this->getLabel(),
            'value_constraints' => $constraints,
            'required' => $this->getRequired(),
        ]);
        $formBuilder->setData($data);

        $fields->append($ffsf);
    }

    public function setRequired(bool $required): FileUploadPagePart
    {
        $this->required = $required;

        return $this;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function setErrorMessageRequired(?string $errorMessageRequired): FileUploadPagePart
    {
        $this->errorMessageRequired = $errorMessageRequired;

        return $this;
    }

    public function getErrorMessageRequired(): ?string
    {
        return $this->errorMessageRequired;
    }

    public function setInternalName(?string $internalName): FileUploadPagePart
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

    public function getDefaultAdminType(): string
    {
        return {{ adminType }}::class;
    }
}
