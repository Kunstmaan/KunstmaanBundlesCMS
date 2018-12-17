<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

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
            @trigger_error('Instantiating the "%s" class is deprecated in KunstmaanArticleBundle 5.1 and will be made abstract in KunstmaanArticleBundle 6.0. Extend your implementation from this class instead.', E_USER_DEPRECATED);
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
