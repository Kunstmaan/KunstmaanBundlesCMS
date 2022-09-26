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
     * @ORM\Column(name="url", type="string", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'url', type: 'string', nullable: true)]
{% endif %}
    private $url;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="text", type="string", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'text', type: 'string', nullable: true)]
{% endif %}
    private $text;

    /**
     * @var bool
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'open_in_new_window', type: 'boolean', nullable: true)]
{% endif %}
    private $openInNewWindow = false;

    public function setUrl(?string $url): LinkPagePart
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getOpenInNewWindow(): bool
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow(bool $openInNewWindow): LinkPagePart
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setText(?string $text): LinkPagePart
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
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
