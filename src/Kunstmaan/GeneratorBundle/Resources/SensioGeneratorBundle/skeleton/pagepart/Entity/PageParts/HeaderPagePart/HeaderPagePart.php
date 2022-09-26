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
     * @var int|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="niv", type="integer", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank(message="headerpagepart.niv.not_blank")
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank(message: 'headerpagepart.niv.not_blank')]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'niv', type: 'integer', nullable: true)]
{% endif %}
    private $niv;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="title", type="string", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank(message="headerpagepart.title.not_blank")
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank(message: 'headerpagepart.title.not_blank')]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'title', type: 'string', nullable: true)]
{% endif %}
    private $title;

    /**
     * @var array Supported header sizes
     */
    public static $supportedHeaders = [1, 2, 3, 4, 5, 6];

    public function setNiv(?int $niv): HeaderPagePart
    {
        $this->niv = $niv;

        return $this;
    }

    public function getNiv(): ?int
    {
        return $this->niv;
    }

    public function setTitle(?string $title): HeaderPagePart
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
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
