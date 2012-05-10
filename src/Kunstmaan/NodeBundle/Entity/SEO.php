<?php

namespace Kunstmaan\AdminNodeBundle\Entity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SEO settings
 *
 * @ORM\Entity
 * @ORM\Table(name="seoinformation")
 */
class SEO extends AbstractEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $metadescription;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $metaauthor;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $metakeywords;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $metarobots;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $metarevised;

    public function getMetaAuthor()
    {
        return $this->metaauthor;
    }

    public function setMetaAuthor($meta)
    {
        $this->metaauthor = $meta;
    }

    public function getMetaDescription()
    {
        return $this->metadescription;
    }

    public function setMetaDescription($meta)
    {
        $this->metadescription = $meta;
    }

    public function getMetaKeywords()
    {
        return $this->metakeywords;
    }

    public function setMetaKeywords($meta)
    {
        $this->metakeywords = $meta;
    }

    public function getMetaRobots()
    {
        return $this->metarobots;
    }

    public function setMetaRobots($meta)
    {
        $this->metarobots = $meta;
    }

    public function getMetaRevised()
    {
        return $this->metarevised;
    }

    public function setMetaRevised($meta)
    {
        $this->metarevised = $meta;
    }
}
