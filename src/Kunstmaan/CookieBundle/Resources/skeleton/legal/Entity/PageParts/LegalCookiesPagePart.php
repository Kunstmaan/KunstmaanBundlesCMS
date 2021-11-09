<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Symfony\Component\Validator\Constraints as Assert;
use {{ namespace }}\Form\PageParts\LegalCookiesPagePartAdminType;

/**
 * LegalCookiesPagePart
 *
 * @ORM\Table(name="{{ prefix }}legal_cookies_page_parts")
 * @ORM\Entity
 */
class LegalCookiesPagePart extends AbstractPagePart
{
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts{% if not isV4 %}:{% else %}/{% endif %}LegalCookiesPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalCookiesPagePartAdminType::class;
    }
}
