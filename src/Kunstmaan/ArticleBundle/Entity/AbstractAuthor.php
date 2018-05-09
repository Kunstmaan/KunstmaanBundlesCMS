<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;

/**
 * Class AbstractAuthor
 */
class AbstractAuthor extends AbstractEntity
{
    /**
     * AbstractAuthor constructor.
     */
    public function __construct()
    {
        if (get_class($this) === AbstractAuthor::class) {
            trigger_error('Please extend this class, it will be made abstract in 6.0.', E_USER_DEPRECATED);
        }
    }

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

    /**
     * @param $link
     */
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
