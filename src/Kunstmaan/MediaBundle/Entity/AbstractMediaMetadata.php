<?php
namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Form\AbstractType;

/**
 * AbstractMediaMetadata
 */
abstract class AbstractMediaMetadata extends AbstractEntity
{

    /**
     * @var Media
     * @ORM\OneToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $media;

    /**
     * getDefaultAdminType
     *
     * @return AbstractType
     */
    public abstract function getDefaultAdminType();

    /**
     * @param Media $media
     *
     * @return AbstractMediaMetadata
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
