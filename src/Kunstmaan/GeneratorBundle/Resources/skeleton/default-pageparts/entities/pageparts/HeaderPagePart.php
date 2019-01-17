<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class HeaderPagePart extends AbstractPagePart
{
    public const ALIGNMENT = [
        'left' => 'left',
        'center' => 'center',
        'right' => 'right',
    ];

    /**
     * @ORM\Column(name="niv", type="integer", nullable=true)
     * @Assert\NotBlank(message="headerpagepart.niv.not_blank")
     */
    private $niv;

    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     * @Assert\NotBlank(message="headerpagepart.title.not_blank")
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull()
     */
    private $alignment;

    /**
     * @var array Supported header sizes
     */
    public static $supportedHeaders = [1, 2, 3, 4, 5, 6];

    public function setNiv(int $niv): HeaderPagePart
    {
        $this->niv = $niv;

        return $this;
    }

    public function getNiv(): ?int
    {
        return $this->niv;
    }

    public function setTitle(string $title): HeaderPagePart
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    public function setAlignment(string $alignment): HeaderPagePart
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/header_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
