<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\SlidePagePartAdminType;

/**
 * SlidePagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="pagepart_slide")
 */
class SlidePagePart extends AbstractPagePart
{

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     */
    public $media;

    /**
     * Get media
     *
     * @return Kunstmaan\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set media
     *
     * @param Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanMediaPagePartBundle:SlidePagePart:view.html.twig";
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
        return new SlidePagePartAdminType();
    }
}
