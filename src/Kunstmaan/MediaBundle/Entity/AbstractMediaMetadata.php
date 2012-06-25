<?php
namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

abstract class AbstractMediaMetadata extends AbstractEntity
{

    /**
     * @ORM\OneToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $media;

    public abstract function getDefaultAdminType();

    /**
     * @param $media
     *
     * @return \AbstractMediaMetadata
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }
}
