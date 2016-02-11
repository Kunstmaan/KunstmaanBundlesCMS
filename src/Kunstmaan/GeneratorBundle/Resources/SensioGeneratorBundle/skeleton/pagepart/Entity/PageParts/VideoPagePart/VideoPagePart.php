<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="video_media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    protected $video;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="caption", nullable=true)
     */
    protected $caption;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="thumbnail_media_id", referencedColumnName="id")
     * })
     */
    protected $thumbnail;

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
	$this->caption = $caption;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
	return $this->caption;
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
	$this->thumbnail = $thumbnail;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getThumbnail()
    {
	return $this->thumbnail;
    }

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $video
     */
    public function setVideo($video)
    {
	$this->video = $video;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getVideo()
    {
	return $this->video;
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
	return "{{ bundle }}:PageParts/{{ pagepart }}:view.html.twig";
    }

    /**
     * @return VideoPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new {{ adminType }}();
    }
}
