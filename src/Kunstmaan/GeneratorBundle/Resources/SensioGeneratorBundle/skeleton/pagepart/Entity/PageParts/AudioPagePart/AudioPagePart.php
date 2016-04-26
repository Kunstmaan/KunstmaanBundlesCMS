<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * {{ pagepart }}
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;

    /**
     * Get media
     *
     * @return Media
     */
    public function getMedia()
    {
	return $this->media;
    }

    /**
     * Set media
     *
     * @param Media $media
     *
     * @return {{ pagepart }}
     */
    public function setMedia($media)
    {
	$this->media = $media;

	return $this;
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
	return "{{ bundle }}:PageParts/{{ pagepart }}:view.html.twig";
    }

    /**
     * @return {{ pagepart }}AdminType
     */
    public function getDefaultAdminType()
    {
	return new {{ adminType }}();
    }
}
