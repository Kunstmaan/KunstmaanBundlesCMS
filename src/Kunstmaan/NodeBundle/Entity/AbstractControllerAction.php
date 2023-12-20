<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Form\ControllerActionAdminType;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractControllerAction extends AbstractEntity implements HasNodeInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    #[ORM\Column(name: 'title', type: 'string')]
    #[Assert\NotBlank]
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="page_title")
     */
    #[ORM\Column(name: 'page_title', type: 'string', nullable: true)]
    protected $pageTitle;

    /**
     * @var HasNodeInterface
     */
    protected $parent;

    /**
     * @param string $title
     *
     * @return AbstractControllerAction
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return HasNodeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return AbstractControllerAction
     */
    public function setParent(HasNodeInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getDefaultAdminType()
    {
        return ControllerActionAdminType::class;
    }
}
