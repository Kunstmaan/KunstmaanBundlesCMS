<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Bike
 *
 * @ORM\Table(name="{{ prefix }}bike")
 * @ORM\Entity
 */
class Bike extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    const TYPE_CITY_BIKE = 'city_bike';
    const TYPE_MOUNTAIN_BIKE = 'mountain_bike';
    const TYPE_RACING_BIKE = 'racing_bike';

    /**
     * @var array Supported bike types
     */
    public static $types = array(
	self::TYPE_CITY_BIKE,
	self::TYPE_MOUNTAIN_BIKE,
	self::TYPE_RACING_BIKE
    );

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $brand;

    /**
     * @var double
     *
     * @ORM\Column(name="price", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $price;

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
}
