<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Symfony\Component\Validator\Constraints as Assert;
use {{ namespace }}\Form\PageParts\LegalTipPagePartAdminType;

/**
 * LegalTipPagePart
 *
 * @ORM\Table(name="{{ prefix }}legal_tip_page_parts")
 * @ORM\Entity
 */
class LegalTipPagePart extends AbstractPagePart
{
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

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
     * @return LegalTipPagePart
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
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts{% if not isV4 %}:{% else %}/{% endif %}LegalTipPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalTipPagePartAdminType::class;
    }
}
