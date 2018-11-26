<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType;

/**
 * ToTopPagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_to_top_page_parts")
 */
class ToTopPagePart extends AbstractPagePart
{
    /**
     * @return string
     */
    public function __toString()
    {
        return 'ToTopPagePart';
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'KunstmaanPagePartBundle:ToTopPagePart:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return ToTopPagePartAdminType::class;
    }
}
