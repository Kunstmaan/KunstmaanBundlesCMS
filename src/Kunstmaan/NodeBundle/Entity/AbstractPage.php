<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Kunstmaan\AdminBundle\Entity\DeepCloneableInterface;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\PageInterface;
use Kunstmaan\AdminBundle\Form\PageAdminType;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * The Abstract ORM Page
 */
abstract class AbstractPage extends AbstractEntity implements PageInterface, DeepCloneableInterface
{

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $pageTitle;

    /**
     * @var HasNodeInterface $parent
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
     * @param string $title
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
     * @return PageAdminType
     */
    public function getDefaultAdminType()
    {
        return new PageAdminType();
    }

    /**
     * @return boolean
     */
    public function isOnline()
    {
        return false;
    }

    /**
     * @return array
     */
    abstract public function getPossibleChildPageTypes();

    /**
     * @param EntityManager $em
     *
     * @return AbstractPage
     */
    public function deepClone(EntityManager $em)
    {
        $newPage = clone $this;
        $newPage->setId(null);
        $em->persist($newPage);
        $em->flush();

        if (method_exists($this, 'getPagePartAdminConfigurations')) {
            $ppConfigurations = $this->getPagePartAdminConfigurations();
            foreach ($ppConfigurations as $ppConfiguration) {
                $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->copyPageParts($em, $this, $newPage, $ppConfiguration->getDefaultContext());
            }
        }

        return $newPage;
    }

    /**
     * @param ContainerInterface $container The Container
     * @param Request            $request   The Request
     * @param array              &$result   The Result array
     */
    public function service(ContainerInterface $container, Request $request, &$result)
    {
    }

}
