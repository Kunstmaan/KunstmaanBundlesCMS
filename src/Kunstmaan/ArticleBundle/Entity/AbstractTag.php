<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractTag extends AbstractEntity implements Translatable
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Gedmo\Translatable
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    #[Gedmo\Translatable]
    #[Assert\NotBlank]
    protected $name;

    /**
     * @Gedmo\Locale
     */
    #[Gedmo\Locale]
    protected $locale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    protected $deletedAt;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
