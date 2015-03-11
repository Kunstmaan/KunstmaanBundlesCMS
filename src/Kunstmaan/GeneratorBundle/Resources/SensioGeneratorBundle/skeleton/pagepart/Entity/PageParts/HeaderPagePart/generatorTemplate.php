<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use {{ namespace }}\Entity\PageParts\AbstractPagePart;
use Symfony\Component\Validator\Constraint as Assert;

/**
 * {{ pagepart }}
 *
 * @ORM\Table(name="kuma_{{ pagepartname }}_page_parts")
 * @ORM\Entity
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $niv;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('niv', new NotBlank(array('message' => 'headerpagepart.niv.not_blank')));
        $metadata->addPropertyConstraint('title', new NotBlank(array('message' => 'headerpagepart.title.not_blank')));
    }

    /**
     * Set niv
     *
     * @param int $niv
     *
     * @return {{ pagepart }}
     */
    public function setNiv($niv)
    {
        $this->niv = $niv;

        return $this;
    }

    /**
     * Get niv
     *
     * @return int
     */
    public function getNiv()
    {
        return $this->niv;
    }

    /**
     * @param string $title
     *
     * @return {{ pagepart }}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "HeaderPagePart " . $this->getTitle();
    }
}