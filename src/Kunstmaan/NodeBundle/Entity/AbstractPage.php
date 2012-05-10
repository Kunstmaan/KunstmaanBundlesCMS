<?php

namespace Kunstmaan\AdminNodeBundle\Entity;
use Kunstmaan\AdminBundle\Entity\DeepCloneableInterface;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\AdminBundle\Form\PageAdminType;

use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\FormBundle\Entity\FormSubmission;

use Doctrine\ORM\EntityManager;


use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminBundle\Entity\PageInterface;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The Abstract ORM Page
 */
abstract class AbstractPage extends AbstractEntity implements PageInterface, DeepCloneableInterface
{

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    protected $parent;

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(HasNodeInterface $parent)
    {
        $this->parent = $parent;
    }

    protected $possiblePermissions = array('read', 'write', 'delete');


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
     * {@inheritdoc}
     */
    public function isOnline()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPossiblePermissions()
    {
        return $this->possiblePermissions;
    }
    
    /**
     * @return array
     */
    public abstract function getPossibleChildPageTypes();

    /**
     * {@inheritdoc}
     */
    public function deepClone(EntityManager $em)
    {
        $newpage = clone $this;
        $em->persist($newpage);
        $em->flush();

        return $newpage;
    }

    /**
     * @param ContainerInterface $container The Container
     * @param Request            $request   The Request
     * @param array              &$result   The Result array
     */
    public function service($container, Request $request, &$result)
    {
    }

}
