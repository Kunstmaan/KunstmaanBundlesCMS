<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}bike')]
{% else %}
/**
 * @ORM\Table(name="{{ prefix }}bike")
 * @ORM\Entity
 */
{% endif %}
class Bike extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    public const TYPE_CITY_BIKE = 'city_bike';
    public const TYPE_MOUNTAIN_BIKE = 'mountain_bike';
    public const TYPE_RACING_BIKE = 'racing_bike';

    /**
     * @var array Supported bike types
     */
    public static $types = [
        self::TYPE_CITY_BIKE,
        self::TYPE_MOUNTAIN_BIKE,
        self::TYPE_RACING_BIKE,
    ];

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'type', type: 'string', length: 20, nullable: true)]
{% endif %}
    private $type;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="model", type="string", length=100, nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'model', type: 'string', length: 100, nullable: true)]
{% endif %}
    private $model;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="brand", type="string", length=100, nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'brand', type: 'string', length: 100, nullable: true)]
{% endif %}
    private $brand;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="price", type="decimal", precision=8, scale=2, nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'price', type: 'decimal', precision: 8, scale: 2, nullable: true)]
{% endif %}
    private $price;

    /**
     * @var integer
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="smallint", nullable=true)
{% if canUseAttributes == false %}
     * @Assert\Type(type = "numeric")
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\Type(type: 'numeric')]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'weight', type: 'smallint', nullable: true)]
{% endif %}
    private $weight;

    /**
     * @return string
     */
    public function getType()
    {
	return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Bike
     */
    public function setType($type)
    {
	$this->type = $type;

	return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
	return $this->model;
    }

    /**
     * @param string $model
     *
     * @return Bike
     */
    public function setModel($model)
    {
	$this->model = $model;

	return $this;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
	return $this->brand;
    }

    /**
     * @param string $brand
     *
     * @return Bike
     */
    public function setBrand($brand)
    {
	$this->brand = $brand;

	return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
	return $this->price;
    }

    /**
     * @param float $price
     *
     * @return Bike
     */
    public function setPrice($price)
    {
	$this->price = $price;

	return $this;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return Bike
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
}
