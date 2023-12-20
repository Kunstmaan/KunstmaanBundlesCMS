<?php

namespace Kunstmaan\CookieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\CookieBundle\Repository\CookieTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="kuma_cookie_types")
 * @ORM\Entity(repositoryClass="Kunstmaan\CookieBundle\Repository\CookieTypeRepository")
 */
#[ORM\Table(name: 'kuma_cookie_types')]
#[ORM\Entity(repositoryClass: CookieTypeRepository::class)]
class CookieType extends AbstractEntity implements OverviewNavigationInterface
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
     * @ORM\Column(name="short_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'short_description', type: 'text', nullable: true)]
    #[Gedmo\Translatable]
    private $shortDescription;

    /**
     * @var string
     *
     * @Gedmo\Translatable()
     * @ORM\Column(name="long_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'long_description', type: 'text', nullable: true)]
    #[Gedmo\Translatable]
    private $longDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_name", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'internal_name', type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private $internalName;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    #[ORM\Column(name: 'weight', type: 'integer', nullable: true)]
    private $weight = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="always_on", type="boolean")
     */
    #[ORM\Column(name: 'always_on', type: 'boolean')]
    private $alwaysOn = false;

    /**
     * @ORM\OneToMany(targetEntity="Kunstmaan\CookieBundle\Entity\Cookie", mappedBy="type", cascade={"ALL"})
     */
    #[ORM\OneToMany(targetEntity: Cookie::class, mappedBy: 'type', cascade: ['ALL'])]
    private $cookies;

    public function __construct()
    {
        $this->cookies = new ArrayCollection();
    }

    /**
     * @param string $name
     *
     * @return CookieType
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
     * @param string $shortDescription
     *
     * @return CookieType
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $longDescription
     *
     * @return CookieType
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param string $internalName
     *
     * @return CookieType
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param int $weight
     *
     * @return CookieType
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param bool $alwaysOn
     *
     * @return CookieType
     */
    public function setAlwaysOn($alwaysOn)
    {
        $this->alwaysOn = $alwaysOn;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAlwaysOn()
    {
        return $this->alwaysOn;
    }

    /**
     * @return bool
     */
    public function getAlwaysOn()
    {
        return $this->alwaysOn;
    }

    /**
     * Add cookie
     *
     * @return CookieType
     */
    public function addCooky(Cookie $cookie)
    {
        $this->cookies[] = $cookie;

        return $this;
    }

    /**
     * Remove cookie
     */
    public function removeCooky(Cookie $cookie)
    {
        $this->cookies->removeElement($cookie);
    }

    /**
     * @return Collection
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return string
     */
    public function getOverViewRoute()
    {
        return 'kunstmaancookiebundle_admin_cookietype';
    }
}
