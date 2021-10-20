<?php

namespace Kunstmaan\CookieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cookie
 *
 * @ORM\Table(name="kuma_cookies")
 * @ORM\Entity(repositoryClass="Kunstmaan\CookieBundle\Repository\CookieRepository")
 */
class Cookie extends AbstractEntity
{
    /**
     * @var string
     * @Gedmo\Translatable()
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Translatable()
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\CookieBundle\Entity\CookieType", inversedBy="cookies")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @var string
     * @Assert\NotNull()
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=true)
     */
    private $domain;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Cookie
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set description
     *
     * @param string $description
     *
     * @return Cookie
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param CookieType $type
     *
     * @return Cookie
     */
    public function setType(CookieType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return CookieType
     */
    public function getType()
    {
        return $this->type;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }
}
