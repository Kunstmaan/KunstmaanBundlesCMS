<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Symfony\Component\Validator\Constraints as Assert;
use {{ namespace }}\Form\PageParts\LegalIconTextPagePartAdminType;

/**
 * LegalIconTextPagePart
 *
 * @ORM\Table(name="{{ prefix }}legal_icon_text_page_parts")
 * @ORM\Entity
 */
class LegalIconTextPagePart extends AbstractPagePart
{
    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * })
     */
    private $icon;

    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(name="subtitle", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $subtitle;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * Set icon
     *
     * @param Media $icon
     *
     * @return LegalIconTextPagePart
     */
    public function setIcon(Media $icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return Media
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param mixed $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }


    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return LegalIconTextPagePart
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts{% if not isV4 %}:{% else %}/{% endif %}LegalIconTextPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalIconTextPagePartAdminType::class;
    }
}
