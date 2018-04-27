<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The Abstract ORM Page
 */
abstract class AbstractPage extends AbstractEntity implements PageInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="page_title")
     */
    protected $pageTitle;

    /**
     * @var HasNodeInterface
     */
    protected $parent;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return AbstractPage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set pagetitle
     *
     * @param string $pageTitle
     *
     * @return AbstractPage
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * Get pagetitle
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        } else {
            return $this->getTitle();
        }
    }

    /**
     * @return HasNodeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param HasNodeInterface $parent
     *
     * @return AbstractPage
     */
    public function setParent(HasNodeInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return PageAdminType::class;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Using the service action is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0. Use the SlugActionInterface instead
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        @trigger_error('Using the service action is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0. Use the SlugActionInterface instead', E_USER_DEPRECATED);
    }

    /**
     * By default this will return false. Pages will always be pages until some class says otherwise.
     *
     * {@inheritdoc}
     */
    public function isStructureNode()
    {
        return false;
    }
}
