<?php

namespace Kunstmaan\CookieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\CookieBundle\Repository\CookieRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="kuma_cookies")
 * @ORM\Entity(repositoryClass="Kunstmaan\CookieBundle\Repository\CookieRepository")
 */
#[ORM\Table(name: 'kuma_cookies')]
#[ORM\Entity(repositoryClass: CookieRepository::class)]
class Cookie extends AbstractEntity
{
    /**
     * @var string
     *
     * @Gedmo\Translatable()
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
    #[Gedmo\Translatable]
    #[Assert\NotBlank]
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Translatable()
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[Gedmo\Translatable]
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\CookieBundle\Entity\CookieType", inversedBy="cookies")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: CookieType::class, inversedBy: 'cookies')]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotBlank]
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'domain', type: 'string', length: 255, nullable: true)]
    #[Assert\NotNull]
    private $domain;

    /**
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Cookie
     */
    public function setType(?CookieType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
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
