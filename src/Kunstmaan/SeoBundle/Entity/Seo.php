<?php

namespace Kunstmaan\SeoBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\SeoBundle\Form\SeoType;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Seo metadata for entities
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\SeoBundle\Repository\SeoRepository")
 * @ORM\Table(name="kuma_seo", indexes={@ORM\Index(name="idx_lookup", columns={"ref_id", "ref_entity_name"})})
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Seo extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", nullable=true)
     */
    protected $metaTitle;

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
     * @var int
     *
     * @ORM\Column(type="bigint", name="ref_id")
     */
    protected $refId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="ref_entity_name")
     */
    protected $refEntityName;

    /**
     * @ORM\Column(type="string", nullable=true, name="linked_in_recommend_link")
     */
    protected $linkedInRecommendLink;

    /**
     * @ORM\Column(type="string", nullable=true, name="linked_in_recommend_product_id")
     */
    protected $linkedInRecommendProductID;

    /**
     * @ORM\Column(type="string", nullable=true, name="og_url")
     */
    protected $ogUrl;

    /**
     * @param string $url
     *
     * @return Seo
     */
    public function setOgUrl($url)
    {
        $this->ogUrl = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getOgUrl()
    {
        return $this->ogUrl;
    }

    /**
     * @param string $var
     *
     * @return Seo
     */
    public function setLinkedInRecommendProductID($var)
    {
        $this->linkedInRecommendProductID = $var;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedInRecommendProductID()
    {
        return $this->linkedInRecommendProductID;
    }

    /**
     * @param string $var
     *
     * @return Seo
     */
    public function setLinkedInRecommendLink($var)
    {
        $this->linkedInRecommendLink = $var;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedInRecommendLink()
    {
        return $this->linkedInRecommendLink;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $title
     *
     * @return string
     */
    public function setMetaTitle($title)
    {
        $this->metaTitle = $title;

        return $this;
    }

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
     * @return Seo
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
     * @return Seo
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
     * @return Seo
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
     * @return Seo
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
     * @return Seo
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
     * @return Seo
     */
    public function setExtraMetadata($extraMetadata)
    {
        $this->extraMetadata = $extraMetadata;

        return $this;
    }

    /**
     * @param string $ogDescription
     *
     * @return Seo
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
     * @return Seo
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
     * @return Seo
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
     * @return Seo
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
     * Get refId
     *
     * @return int
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set refId
     *
     * @param int $refId
     *
     * @return Seo
     */
    protected function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Set reference entity name
     *
     * @param string $refEntityName
     *
     * @return Seo
     */
    protected function setRefEntityName($refEntityName)
    {
        $this->refEntityName = $refEntityName;

        return $this;
    }

    /**
     * Get reference entity name
     *
     * @return string
     */
    public function getRefEntityName()
    {
        return $this->refEntityName;
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return Seo
     */
    public function setRef(AbstractEntity $entity)
    {
        $this->setRefId($entity->getId());
        $this->setRefEntityName(ClassLookup::getClass($entity));

        return $this;
    }

    /**
     * @param EntityManager $em
     *
     * @return AbstractEntity
     */
    public function getRef(EntityManager $em)
    {
        return $em->getRepository($this->getRefEntityName())->find($this->getRefId());
    }

    /**
     * @return SeoType
     */
    public function getDefaultAdminType()
    {
        return new SeoType();
    }

}
