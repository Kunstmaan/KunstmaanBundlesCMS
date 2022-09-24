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
     * @var string|null
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
     * @var string|null
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
     * @var string|null
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
     * @var string|null
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
     * @var int|null
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Bike
    {
        $this->type = $type;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): Bike
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): Bike
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): Bike
    {
        $this->price = $price;

        return $this;
    }

    public function setWeight(int $weight): Bike
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }
}
