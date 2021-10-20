<?php

namespace Kunstmaan\CookieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CookieType
 *
 * @ORM\Table(name="kuma_cookie_types")
 * @ORM\Entity(repositoryClass="Kunstmaan\CookieBundle\Repository\CookieTypeRepository")
 */
class CookieType extends AbstractEntity implements OverviewNavigationInterface
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
     * @ORM\Column(name="short_description", type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     * @Gedmo\Translatable()
     *
     * @ORM\Column(name="long_description", type="text", nullable=true)
     */
    private $longDescription;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="internal_name", type="string", length=255, nullable=true)
     */
    private $internalName;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="always_on", type="boolean")
     */
    private $alwaysOn = false;

    /**
     * @ORM\OneToMany(targetEntity="Kunstmaan\CookieBundle\Entity\Cookie", mappedBy="type", cascade={"ALL"})
     */
    private $cookies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cookies = new ArrayCollection();
    }

    /**
     * Set name
     *
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set shortDescription
     *
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
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set longDescription
     *
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
     * Get longDescription
     *
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * Set internalName
     *
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
     * Get internalName
     *
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Set weight
     *
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
     * Get weight
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set alwaysOn
     *
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
     * Get alwaysOn
     *
     * @return bool
     */
    public function isAlwaysOn()
    {
        return $this->alwaysOn;
    }

    /**
     * Get alwaysOn
     *
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
     * Get cookies
     *
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
