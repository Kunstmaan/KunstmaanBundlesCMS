<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class RawHtmlPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $content;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): RawHtmlPagePart
    {
        $this->content = $content;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/raw_html_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
