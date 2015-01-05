<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Form\NodeTranslationAdminType;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;

/**
 * NodeTranslation
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\NodeBundle\Repository\NodeTranslationRepository")
 * @ORM\Table(
 *     name="kuma_node_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ix_kuma_node_translations_node_lang", columns={"node_id", "lang"})},
 *     indexes={@ORM\Index(name="idx_lang_url", columns={"lang", "url"})}
 * )
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class NodeTranslation extends AbstractEntity
{

    /**
     * @var Node
     *
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="nodeTranslations")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    protected $node;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $lang;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $online = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @var NodeVersion
     *
     * @ORM\ManyToOne(targetEntity="NodeVersion")
     * @ORM\JoinColumn(name="public_node_version_id", referencedColumnName="id")
     */
    protected $publicNodeVersion;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="NodeVersion", mappedBy="nodeTranslation")
     * @ORM\OrderBy({"created" = "ASC"})
     */
    protected $nodeVersions;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $weight;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * contructor
     */
    public function __construct()
    {
        $this->nodeVersions = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Set node
     *
     * @param Node $node
     *
     * @return NodeTranslation
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get Node
     *
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set lang
     *
     * @param string $lang
     *
     * @return NodeTranslation
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Is online
     *
     * @return bool
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * Set online
     *
     * @param bool $online
     *
     * @return NodeTranslation
     */
    public function setOnline($online)
    {
        $this->online = $online;

        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return NodeTranslation
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
     * Set slug
     *
     * @param string $slug
     *
     * @return NodeTranslation
     */
    public function setSlug($slug)
    {
        $this->slug = Slugifier::slugify($slug, '');

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getFullSlug()
    {
        $slug = $this->getSlugPart();

        if (empty($slug)) {
            return null;
        }

        return $slug;
    }

    /**
     * @return string
     */
    public function getSlugPart()
    {
        $slug       = "";
        $parentNode = $this->getNode()->getParent();
        if ($parentNode !== null) {
            $nodeTranslation = $parentNode->getNodeTranslation($this->lang, true);

            if ($nodeTranslation !== null) {
                $parentSlug = $nodeTranslation->getSlugPart();
                if (!empty($parentSlug)) {
                    $slug = rtrim($parentSlug, "/") . "/";
                }
            }
        }
        $slug = $slug . $this->getSlug();

        return $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param NodeVersion $publicNodeVersion
     *
     * @return NodeTranslation
     */
    public function setPublicNodeVersion(NodeVersion $publicNodeVersion)
    {
        $this->publicNodeVersion = $publicNodeVersion;

        return $this;
    }

    /**
     * @return NodeVersion
     */
    public function getPublicNodeVersion()
    {
        return $this->publicNodeVersion;
    }

    /**
     * @return ArrayCollection
     */
    public function getNodeVersions()
    {
        return $this->nodeVersions;
    }

    /**
     * @param ArrayCollection $nodeVersions
     *
     * @return NodeTranslation
     */
    public function setNodeVersions(ArrayCollection $nodeVersions)
    {
        $this->nodeVersions = $nodeVersions;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return NodeVersion|null
     */
    public function getNodeVersion($type)
    {
        $nodeVersions = $this->getNodeVersions();

        $max = count($nodeVersions);
        for ($i = $max-1; $i >= 0; $i--) {
            /* @var NodeVersion $nodeVersion */
            $nodeVersion = $nodeVersions[$i];

            if ($type == $nodeVersion->getType()) {
                return $nodeVersion;
            }
        }

        return null;
    }

    /**
     * Add nodeVersion
     *
     * @param NodeVersion $nodeVersion
     *
     * @return NodeTranslation
     */
    public function addNodeVersion(NodeVersion $nodeVersion)
    {
        $this->nodeVersions[] = $nodeVersion;
        $nodeVersion->setNodeTranslation($this);

        return $this;
    }

    /**
     * @return NodeTranslationAdminType
     */
    public function getDefaultAdminType()
    {
        return new NodeTranslationAdminType();
    }

    /**
     * @param EntityManager $em   The entity manager
     * @param string        $type The type
     *
     * @return object|null
     */
    public function getRef(EntityManager $em, $type = "public")
    {
        $nodeVersion = $this->getNodeVersion($type);
        if ($nodeVersion) {
            return $em->getRepository($nodeVersion->getRefEntityName())->find($nodeVersion->getRefId());
        }

        return null;
    }

    /**
     * @param string $url
     *
     * @return NodeTranslation
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param int $weight
     *
     * @return NodeTranslation
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return NodeTranslation
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @return NodeTranslation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
