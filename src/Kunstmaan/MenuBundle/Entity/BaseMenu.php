<?php

namespace Kunstmaan\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BaseMenu
 * @package Kunstmaan\MenuBundle\Entity
 *
 * @ORM\MappedSuperclass()
 */
class BaseMenu extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=true)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5, nullable=true)
     * @Assert\NotBlank()
     */
    protected $locale;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Kunstmaan\MenuBundle\Entity\MenuItem", mappedBy="menu", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $items;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    /**
     * @param MenuItem $item
     */
    public function addItem(MenuItem $item)
    {
        $item->setMenu($this);

        $this->items->add($item);
    }

    /**
     * @param MenuItem $item
     */
    public function removeItem(MenuItem $item)
    {
        $this->items->removeElement($item);
    }
}
