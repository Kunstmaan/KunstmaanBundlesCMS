<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @experimental This feature is experimental and is a subject to change, be advised when using this feature and classes.
 *
 * @ORM\Entity()
 * @ORM\Table(name="kuma_editable_media_wrapper")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_editable_media_wrapper')]
class EditableMediaWrapper extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id')]
    private $media;

    /**
     * @ORM\Column(name="runtime_config", type="text", nullable=true)
     */
    #[ORM\Column(name: 'runtime_config', type: 'text', nullable: true)]
    private $runTimeConfig;

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): EditableMediaWrapper
    {
        $this->media = $media;

        return $this;
    }

    public function getRunTimeConfig()
    {
        return $this->runTimeConfig;
    }

    public function setRunTimeConfig($runTimeConfig): EditableMediaWrapper
    {
        $this->runTimeConfig = $runTimeConfig;

        return $this;
    }
}
