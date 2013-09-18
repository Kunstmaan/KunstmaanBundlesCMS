<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Satellite
 *
 * @ORM\Table(name="{{ prefix }}_satellite")
 * @ORM\Entity
 */
class Satellite extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    const TYPE_COMMUNICATION = 'communication';
    const TYPE_CLIMATE       = 'climate_research';
    const TYPE_PASSIVE       = 'passive';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="launched", type="date")
     * @Assert\NotBlank()
     */
    private $launched;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string")
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=25)
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"communication", "climate_research", "passive"})
     */
    private $type;

    /**
     * Set name
     *
     * @param string $name
     * @return Satellite
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
     * Set launched
     *
     * @param \DateTime $launched
     * @return Satellite
     */
    public function setLaunched($launched)
    {
        $this->launched = $launched;

        return $this;
    }

    /**
     * Get launched
     *
     * @return \DateTime
     */
    public function getLaunched()
    {
        return $this->launched;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Satellite
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Satellite
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Satellite
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
