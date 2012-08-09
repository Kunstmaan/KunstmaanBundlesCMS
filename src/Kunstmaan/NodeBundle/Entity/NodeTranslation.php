<?php

namespace Kunstmaan\AdminNodeBundle\Entity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Kunstmaan\SearchBundle\Entity\IndexableInterface;

use Kunstmaan\AdminNodeBundle\Form\NodeAdminType;
use Kunstmaan\AdminNodeBundle\Form\NodeTranslationAdminType;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * NodeTranslation
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminNodeBundle\Repository\NodeTranslationRepository")
 * @ORM\Table(name="nodetranslation")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class NodeTranslation extends AbstractEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(name="node", referencedColumnName="id")
     */
    protected $node;

    /**
     * @ORM\Column(type="string")
     */
    protected $lang;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $online = false;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @ORM\ManyToOne(targetEntity="NodeVersion")
     * @ORM\JoinColumn(name="publicNodeVersion", referencedColumnName="id")
     */
    protected $publicNodeVersion;

    /**
     * @ORM\OneToOne(targetEntity="SEO", cascade={"all"})
     * @ORM\JoinColumn(name="seo", referencedColumnName="id")
     */
    protected $seo;

    /**
     * @ORM\OneToMany(targetEntity="NodeVersion", mappedBy="nodeTranslation")
     * @ORM\OrderBy({"version" = "DESC"})
     */
    protected $nodeVersions;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $weight;
    
    public function __construct()
    {
        $this->nodeVersions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->seo = new SEO();
    }

    /**
     * Set node
     *
     * @param integer $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * Get Node
     *
     * @return integer
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set lang
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
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
     * @return boolean
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * Set online
     *
     * @param boolean $online
     */
    public function setOnline($online)
    {
        $this->online = $online;
    }

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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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

    public function getSlugPart()
    {
        $slug = "";
        $parentNode = $this->getNode()->getParent();
        if ($parentNode != null) {
            $nodeTranslation = $parentNode->getNodeTranslation($this->lang);
            if ($nodeTranslation != null) {
                $parentslug = $nodeTranslation->getSlugPart();
                if (!empty($parentslug)) {
                    $slug = rtrim($parentslug, "/") . "/";
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

    public function getParentSlug($node)
    {
        $parentslug = $node->getParent()->getNodeTranslation($this->lang)->getSlug();
        if (!empty($parentslug)) {
            return $parentslug . "/";
        }
        return "";
    }

    /**
     * @param NodeVersion $publicNodeVersion
     */
    public function setPublicNodeVersion($publicNodeVersion)
    {
        $this->publicNodeVersion = $publicNodeVersion;
    }

    /**
     * @return NodeVersion
     */
    public function getPublicNodeVersion()
    {
        return $this->publicNodeVersion;
    }

    /**
     * @return NodeVersion[]
     */
    public function getNodeVersions()
    {
        return $this->nodeVersions;
    }

    /**
     * @param NodeVersion[] $nodeVersions
     */
    public function setNodeVersions($nodeVersions)
    {
        $this->nodeVersions = $nodeVersions;
    }

    public function getNodeVersion($type)
    {
        $nodeVersions = $this->getNodeVersions();
        foreach ($nodeVersions as $nodeVersion) {
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
     */
    public function addNodeVersion(NodeVersion $nodeVersion)
    {
        $this->nodeVersions[] = $nodeVersion;
        $nodeVersion->setNodeTranslation($this);
    }

    public function disableNodeVersionsLazyLoading()
    {
        if (is_object($this->nodeVersions)) {
            $this->nodeVersions->setInitialized(true);
        }
    }

    public function getDefaultAdminType($container)
    {
        return new NodeTranslationAdminType($container);
    }

    public function getRef($em, $type = "public")
    {
        $nodeVersion = $this->getNodeVersion($type);
        if ($nodeVersion) {
            return $em->getRepository($nodeVersion->getRefEntityname())->find($nodeVersion->getRefId());
        }
        return null;
    }

    public function getSearchContentForNode($container, $entity, $field)
    {
        $page = $entity->getRef($container->get('doctrine')->getEntityManager());
        if ($page instanceof IndexableInterface) {
            return $page;
        }

        return null;
    }

    public function getParentsAndSelfForNode($container, $entity, $field)
    {
        $node = $entity->getNode();
        $results = array();
        if ($node->getParent() == null) {
            $parents[] = $node->getId();
        } else {
            $parents = $this->getAllParentsForNode($node, $results);
        }

        return 'start ' . implode(' ', $parents) . ' stop';
    }

    public function getAllParentsForNode($node, $results)
    {
        $parentNode = $node->getParent();
        if (is_object($parentNode)) {
            $results[] = $parentNode->getId();

            return $this->getAllParentsForNode($parentNode, $results);
        } else {
            return $results;
        }
    }

    /**
     * Returns the date the first nodeversion was created
     *
     * @return mixed
     */
    public function getCreated()
    {
        $versions = $this->getNodeVersions();
        $firstVersion = $versions->first();

        return $firstVersion->getCreated();
    }

    /**
     * Returns the date the last nodeversion was updated
     *
     * @return mixed
     */
    public function getUpdated()
    {
        $versions = $this->getNodeVersions();
        $lastVersion = $versions->last();

        return $lastVersion->getUpdated();
    }

    public function setSEO($seo)
    {
        $this->seo = $seo;
    }

    public function getSEO()
    {
        return $this->seo;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
    
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
    
    public function getWeight()
    {
        return $this->weight;
    }

}
