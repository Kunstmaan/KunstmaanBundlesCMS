<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

class AbstractTag extends AbstractEntity implements Translatable
{
    /**
     * AbstractTag constructor.
     */
    public function __construct()
    {
        if (get_class($this) === AbstractTag::class) {
            @trigger_error('Instantiating the "%s" class is deprecated in KunstmaanArticleBundle 5.1 and will be made abstract in KunstmaanArticleBundle 6.0. Extend your implementation from this class instead.', E_USER_DEPRECATED);
        }
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     */
    protected $name;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
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
