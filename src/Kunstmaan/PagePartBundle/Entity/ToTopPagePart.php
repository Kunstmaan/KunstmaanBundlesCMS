<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType;

/**
 * ToTopPagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="totoppagepart")
 */
class ToTopPagePart extends AbstractPagePart
{

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "ToTopPagePart";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:ToTopPagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new ToTopPagePartAdminType();
    }
}
