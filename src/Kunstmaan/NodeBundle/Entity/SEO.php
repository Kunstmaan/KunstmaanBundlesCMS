<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

use JMS\SecurityExtraBundle\Security\Util\String;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminNodeBundle\Form\SEOType;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * SEO settings
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_seo")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class SEO extends AbstractEntity
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_author", type="string", nullable=true)
     */
    protected $metaAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", nullable=true)
     */
    protected $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_robots", type="string", nullable=true)
     */
    protected $metaRobots;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_revised", type="string", nullable=true)
     */
    protected $metaRevised;

    /**
     * @var string
     *
     * @ORM\Column(name="og_type", type="string", nullable=true)
     */
    protected $ogType;

    /**
     * @var string
     *
     * @ORM\Column(name="og_title", type="string", nullable=true)
     */
    protected $ogTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="og_description", type="text", nullable=true)
     */
    protected $ogDescription;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="og_image_id", referencedColumnName="id")
     */
    protected $ogImage;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_metadata", type="text", nullable=true)
     */
    protected $extraMetadata;

    /**
     * @var string
     *
     * @ORM\Column(name="cim_keyword", type="string", length=24, nullable=true)
     * @Assert\Regex(pattern="/^[a-zA-Z0-9\/]*$/")
     */
    public $cimKeyword;

    /**
     * @return string
     */
    public function getMetaAuthor()
    {
        return $this->metaAuthor;
    }

    /**
     * @param string $meta
     *
     * @return SEO
     */
    public function setMetaAuthor($meta)
    {
        $this->metaAuthor = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $meta
     *
     * @return SEO
     */
    public function setMetaDescription($meta)
    {
        $this->metaDescription = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $meta
     *
     * @return SEO
     */
    public function setMetaKeywords($meta)
    {
        $this->metaKeywords = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaRobots()
    {
        return $this->metaRobots;
    }

    /**
     * @param string $meta
     *
     * @return SEO
     */
    public function setMetaRobots($meta)
    {
        $this->metaRobots = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaRevised()
    {
        return $this->metaRevised;
    }

    /**
     * @param string $meta
     *
     * @return SEO
     */
    public function setMetaRevised($meta)
    {
        $this->metaRevised = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtraMetadata()
    {
        return $this->extraMetadata;
    }

    /**
     * @param string $extraMetadata
     *
     * @return SEO
     */
    public function setExtraMetadata($extraMetadata)
    {
        $this->extraMetadata = $extraMetadata;

        return $this;
    }

    /**
     * @param string $ogDescription
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
     * @param Media $ogImage
     *
     * @return SEO
     */
    public function setOgImage(Media $ogImage)
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
     * @param string $ogTitle
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
     * @param string $ogType
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
     *
     * @return SEO
     */
    public function setCimKeyword($cimKeyword)
    {
        // CIM keyword is limited to 24 characters
        if (strlen($cimKeyword) > 24) {
            $cimKeyword = substr($cimKeyword, 0, 24);
        }
        $this->cimKeyword = $cimKeyword;

        return $this;
    }

    /**
     * @return SEOType
     */
    public function getDefaultAdminType()
    {
        return new SEOType();
    }

}
