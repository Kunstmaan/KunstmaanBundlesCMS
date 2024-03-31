<?php

namespace Kunstmaan\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 */
#[ORM\MappedSuperclass]
class BaseMenu
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'bigint')]
    #[ORM\GeneratedValue('AUTO')]
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=true)
     */
    #[ORM\Column(name: 'name', type: 'string', length: 25, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 25)]
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5, nullable=true)
     */
    #[ORM\Column(name: 'locale', type: 'string', length: 5, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 5)]
    protected $locale;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Kunstmaan\MenuBundle\Entity\MenuItem", mappedBy="menu", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    #[ORM\OneToMany(targetEntity: MenuItem::class, mappedBy: 'menu', cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id The unique identifier
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @param string $name
     *
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Menu
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection $items
     *
     * @return Menu
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(MenuItem $item)
    {
        $item->setMenu($this);

        $this->items->add($item);
    }

    public function removeItem(MenuItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Return string representation of entity
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }
}
