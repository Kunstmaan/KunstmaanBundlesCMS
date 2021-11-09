<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Symfony\Component\Validator\Constraints as Assert;
use {{ namespace }}\Form\PageParts\LegalCenteredIconPagePartAdminType;

/**
 * LegalLegalCenteredIconPagePart
 *
 * @ORM\Table(name="{{ prefix }}legal_centered_icon_page_parts")
 * @ORM\Entity
 */
class LegalCenteredIconPagePart extends AbstractPagePart
{
    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * })
     */
    private $icon;

    /**
     * Set icon
     *
     * @param Media $icon
     *
     * @return LegalCenteredIconPagePart
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
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts{% if not isV4 %}:{% else %}/{% endif %}LegalCenteredIconPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalCenteredIconPagePartAdminType::class;
    }
}
