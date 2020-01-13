<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="kuma_croppable_media_link")
 * @ORM\HasLifecycleCallbacks
 */
class CroppableMediaLink extends AbstractEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $media;

    /**
     * @ORM\Column(name="run_time_config", type="text", nullable=true)
     */
    private $runTimeConfig;

    public function getMedia()
    {
        return $this->media;
    }

    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    public function getRunTimeConfig()
    {
        return $this->runTimeConfig;
    }

    public function setRunTimeConfig($runTimeConfig)
    {
        $this->runTimeConfig = $runTimeConfig;

        return $this;
    }
}
