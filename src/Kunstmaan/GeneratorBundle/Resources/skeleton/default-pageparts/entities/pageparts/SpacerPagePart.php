<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class SpacerPagePart extends AbstractPagePart
{
    public const SPACER_SIZES = [
        'xs' => 'xs',
        's' => 's',
        'm' => 'm',
        'l' => 'l',
        'xl' => 'xl',
        'xxl' => 'xxl',
    ];

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $size;

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): SpacerPagePart
    {
        $this->size = $size;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/spacer_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
