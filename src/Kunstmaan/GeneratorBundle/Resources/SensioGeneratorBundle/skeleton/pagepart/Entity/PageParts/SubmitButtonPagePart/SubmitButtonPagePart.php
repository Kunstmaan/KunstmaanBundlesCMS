<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}{{ underscoreName }}s')]
{% else %}
/**
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
{% endif %}
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(type: 'string', nullable: true)]
{% endif %}
    protected $label;

    public function setLabel(?string $label): SubmitButtonPagePart
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function __toString(): string
    {
        return 'SubmitButtonPagePart';
    }

    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle }}:{%endif%}PageParts/{{ pagepart }}{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    public function getAdminView(): string
    {
        return '{% if not isV4 %}{{ bundle }}:{%endif%}PageParts/{{ pagepart }}{% if not isV4 %}:{% else %}/{% endif %}admin-view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ adminType }}::class;
    }
}
