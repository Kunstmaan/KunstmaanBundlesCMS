<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Helper\Generator\ExtensionGuesser;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Assetic\Asset\FileAsset;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\MediaRepository")
 * @ORM\Table(name="media")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"media" = "Media", "image" = "Image", "file" = "File", "slide" = "Slide" , "video" = "Video"})
 * @ORM\HasLifecycleCallbacks
 */
abstract class Media extends AbstractEntity
{

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uuid;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $contentType;

    /**
     * @ORM\Column(type="array")
     */
    protected $metadata;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="files")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    protected $gallery;

    protected $content;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    protected $filesize;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Get context
     *
     * @return string
     */
    public abstract function getContext();

    public function getFileSize()
    {
        $size = $this->filesize;
        if ($size < 1024) return $size . "b";
        else {
            $help = $size / 1024;
            if ($help < 1024) return round($help, 1) . "kb";
            else return round(($help / 1024), 1) . "mb";
        }
    }

    public function setFileSize($filesize)
    {
        $this->filesize = $filesize;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentTypeShort()
    {
        $contentType = $this->contentType;
        $array       = explode("/", $contentType);
        $contentType = end($array);
        return $contentType;
    }

    /**
     * Set metadata
     *
     * @param array $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set content
     *
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Get content
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set gallery
     *
     * @param Kunstmaan\MediaBundle\Entity\Gallery $gallery
     */
    public function setGallery(Folder $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Get gallery
     *
     * @return Kunstmaan\MediaBundle\Entity\Folder
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    public function getUrl()
    {
        return $this->show();
    }


    public function show($format = NULL, $options = array())
    {
        $path = $this->getContext() . "/";
        $path = $path . $this->getUuid();
        if (isset($format)) {
            $path = $path . "_" . $format;
        }
        $path = $path . "." . ExtensionGuesser::guess($this->getContentType());
        return $path;
    }

    public function getClassType()
    {
        $class = explode('\\', get_class($this));
        $classname = end($class);
        return $classname;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}