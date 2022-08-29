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
     * @var string
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

    /**
     * Set niv
     *
     * @param int $niv
     *
     * @return {{ pagepart }}
     */
    public function setNiv($niv)
    {
        $this->niv = $niv;

        return $this;
    }

    /**
     * Get niv
     *
     * @return int
     */
    public function getNiv()
    {
        return $this->niv;
    }

    /**
     * @param string $title
     *
     * @return {{ pagepart }}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle }}:{%endif%}PageParts/{{ pagepart }}{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }
}
