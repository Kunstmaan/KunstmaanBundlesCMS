<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

abstract class AbstractAuthor extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="name")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="link")
     */
    protected $link;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
