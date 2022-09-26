<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="text", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(type: 'text', nullable: true)]
{% endif %}
    protected $content;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): RawHtmlPagePart
    {
        $this->content = $content;

        return $this;
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
