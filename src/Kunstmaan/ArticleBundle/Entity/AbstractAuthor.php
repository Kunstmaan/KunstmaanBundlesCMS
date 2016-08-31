<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;

class AbstractAuthor extends AbstractEntity
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

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getAdminType()
    {
        return new AbstractAuthorAdminType();
    }
}
