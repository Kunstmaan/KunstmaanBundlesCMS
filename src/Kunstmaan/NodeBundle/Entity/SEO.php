<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metadescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaauthor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metakeywords;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metarobots;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metarevised;

    /**
     * @ORM\Column(name="og_type", type="string", nullable=true)
     */
    protected $ogType;

    /**
     * @ORM\Column(name="og_title", type="string", nullable=true)
     */
    protected $ogTitle;

    /**
     * @ORM\Column(name="og_description", type="text", nullable=true)
     */
    protected $ogDescription;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="og_image", referencedColumnName="id")
     */
    protected $ogImage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $extraMetadata;

    /**
     * @ORM\Column(name="cim_keyword", type="string", length=24, nullable=true)
     * @Assert\Regex(pattern="/^[a-zA-Z0-9\/]*$/")
     */
    public $cimKeyword;

    /**
     * @return mixed
     */
    public function getMetaAuthor()
    {
        return $this->metaauthor;
    }

    /**
     * @param $meta
     */
    public function setMetaAuthor($meta)
    {
        $this->metaauthor = $meta;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metadescription;
    }

    /**
     * @param $meta
     */
    public function setMetaDescription($meta)
    {
        $this->metadescription = $meta;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->metakeywords;
    }

    /**
     * @param $meta
     */
    public function setMetaKeywords($meta)
    {
        $this->metakeywords = $meta;
    }

    /**
     * @return mixed
     */
    public function getMetaRobots()
    {
        return $this->metarobots;
    }

    /**
     * @param $meta
     */
    public function setMetaRobots($meta)
    {
        $this->metarobots = $meta;
    }

    /**
     * @return mixed
     */
    public function getMetaRevised()
    {
        return $this->metarevised;
    }

    /**
     * @param $meta
     */
    public function setMetaRevised($meta)
    {
        $this->metarevised = $meta;
    }

    /**
     * @return mixed
     */
    public function getExtraMetadata()
    {
        return $this->extraMetadata;
    }

    /**
     * @param $extraMetadata
     */
    public function setExtraMetadata($extraMetadata)
    {
        $this->extraMetadata = $extraMetadata;
    }

    /**
     * @param $ogDescription
     *
     * @return SEO
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * @param $ogImage
     *
     * @return SEO
     */
    public function setOgImage($ogImage)
    {
        $this->ogImage = $ogImage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgImage()
    {
        return $this->ogImage;
    }

    /**
     * @param $ogTitle
     *
     * @return SEO
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgTitle()
    {
        return $this->ogTitle;
    }

    /**
     * @param $ogType
     *
     * @return SEO
     */
    public function setOgType($ogType)
    {
        $this->ogType = $ogType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgType()
    {
        return $this->ogType;
    }

    /**
     * @return string
     */
    public function getCimKeyword()
    {
        return $this->cimKeyword;
    }

    /**
     * @param string $cimKeyword
     */
    public function setCimKeyword($cimKeyword)
    {
        // CIM keyword is limited to 24 characters
        if (strlen($cimKeyword) > 24) {
            $cimKeyword = substr($cimKeyword, 0, 24);
        }
        $this->cimKeyword = $cimKeyword;
    }

}
