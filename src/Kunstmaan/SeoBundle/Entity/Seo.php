<?php

namespace Kunstmaan\SeoBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\SeoBundle\Form\SeoType;
use Kunstmaan\SeoBundle\Repository\SeoRepository;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Seo metadata for entities
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\SeoBundle\Repository\SeoRepository")
 * @ORM\Table(name="kuma_seo", indexes={@ORM\Index(name="idx_seo_lookup", columns={"ref_id", "ref_entity_name"})})
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
#[ORM\Entity(repositoryClass: SeoRepository::class)]
#[ORM\Table(name: 'kuma_seo')]
#[ORM\Index(name: 'idx_seo_lookup', columns: ['ref_id', 'ref_entity_name'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Seo extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", nullable=true)
     */
    #[ORM\Column(name: 'meta_title', type: 'string', nullable: true)]
    protected $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'meta_description', type: 'text', nullable: true)]
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_author", type="string", nullable=true)
     */
    #[ORM\Column(name: 'meta_author', type: 'string', nullable: true)]
    protected $metaAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_robots", type="string", nullable=true)
     */
    #[ORM\Column(name: 'meta_robots', type: 'string', nullable: true)]
    protected $metaRobots;

    /**
     * @var string
     *
     * @ORM\Column(name="og_type", type="string", nullable=true)
     */
    #[ORM\Column(name: 'og_type', type: 'string', nullable: true)]
    protected $ogType;

    /**
     * @var string
     *
     * @ORM\Column(name="og_title", type="string", nullable=true)
     */
    #[ORM\Column(name: 'og_title', type: 'string', nullable: true)]
    protected $ogTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="og_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'og_description', type: 'text', nullable: true)]
    protected $ogDescription;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="og_image_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'og_image_id', referencedColumnName: 'id')]
    protected $ogImage;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_metadata", type="text", nullable=true)
     */
    #[ORM\Column(name: 'extra_metadata', type: 'text', nullable: true)]
    protected $extraMetadata;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", name="ref_id")
     */
    #[ORM\Column(name: 'ref_id', type: 'bigint')]
    protected $refId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="ref_entity_name")
     */
    #[ORM\Column(name: 'ref_entity_name', type: 'string')]
    protected $refEntityName;

    /**
     * @ORM\Column(type="string", nullable=true, name="og_url")
     */
    #[ORM\Column(name: 'og_url', type: 'string', nullable: true)]
    protected $ogUrl;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, name="og_article_author")
     */
    #[ORM\Column(name: 'og_article_author', type: 'string', length: 100, nullable: true)]
    protected $ogArticleAuthor;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, name="og_article_publisher")
     */
    #[ORM\Column(name: 'og_article_publisher', type: 'string', length: 100, nullable: true)]
    protected $ogArticlePublisher;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, name="og_article_section")
     */
    #[ORM\Column(name: 'og_article_section', type: 'string', length: 100, nullable: true)]
    protected $ogArticleSection;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_title", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'twitter_title', type: 'string', length: 255, nullable: true)]
    protected $twitterTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'twitter_description', type: 'text', nullable: true)]
    protected $twitterDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_site", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'twitter_site', type: 'string', length: 255, nullable: true)]
    protected $twitterSite;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_creator", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'twitter_creator', type: 'string', length: 255, nullable: true)]
    protected $twitterCreator;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="twitter_image_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'twitter_image_id', referencedColumnName: 'id')]
    protected $twitterImage;

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

    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * @return Seo
     */
    public function setOgImage(?Media $ogImage = null)
    {
        $this->ogImage = $ogImage;

        return $this;
    }

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

    public function getOgType()
    {
        return $this->ogType;
    }

    public function getOgArticleAuthor()
    {
        return $this->ogArticleAuthor;
    }

    public function setOgArticleAuthor($ogArticleAuthor)
    {
        $this->ogArticleAuthor = $ogArticleAuthor;
    }

    public function getOgArticlePublisher()
    {
        return $this->ogArticlePublisher;
    }

    public function setOgArticlePublisher($ogArticlePublisher)
    {
        $this->ogArticlePublisher = $ogArticlePublisher;
    }

    public function getOgArticleSection()
    {
        return $this->ogArticleSection;
    }

    public function setOgArticleSection($ogArticleSection)
    {
        $this->ogArticleSection = $ogArticleSection;
    }

    /**
     * @return string
     */
    public function getTwitterTitle()
    {
        return $this->twitterTitle;
    }

    /**
     * @param string $twitterTitle
     */
    public function setTwitterTitle($twitterTitle)
    {
        $this->twitterTitle = $twitterTitle;
    }

    /**
     * @return string
     */
    public function getTwitterDescription()
    {
        return $this->twitterDescription;
    }

    /**
     * @param string $twitterDescription
     */
    public function setTwitterDescription($twitterDescription)
    {
        $this->twitterDescription = $twitterDescription;
    }

    /**
     * @return string
     */
    public function getTwitterSite()
    {
        return $this->twitterSite;
    }

    /**
     * @param string $twitterSite
     */
    public function setTwitterSite($twitterSite)
    {
        $this->twitterSite = $twitterSite;
    }

    /**
     * @return string
     */
    public function getTwitterCreator()
    {
        return $this->twitterCreator;
    }

    /**
     * @param string $twitterCreator
     */
    public function setTwitterCreator($twitterCreator)
    {
        $this->twitterCreator = $twitterCreator;
    }

    /**
     * @return Media
     */
    public function getTwitterImage()
    {
        return $this->twitterImage;
    }

    /**
     * @param Media $twitterImage
     */
    public function setTwitterImage($twitterImage)
    {
        $this->twitterImage = $twitterImage;
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
     * @return Seo
     */
    public function setRef(AbstractEntity $entity)
    {
        $this->setRefId($entity->getId());
        $this->setRefEntityName(ClassLookup::getClass($entity));

        return $this;
    }

    /**
     * @return AbstractEntity
     */
    public function getRef(EntityManagerInterface $em)
    {
        return $em->getRepository($this->getRefEntityName())->find($this->getRefId());
    }

    /**
     * @return string
     */
    public function getDefaultAdminType()
    {
        return SeoType::class;
    }
}
