<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="{{ table_name }}")
 */
class AudioPagePart extends AbstractPagePart
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $media;

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): AudioPagePart
    {
        $this->media = $media;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/audio_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
