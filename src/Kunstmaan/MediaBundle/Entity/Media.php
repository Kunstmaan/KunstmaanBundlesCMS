<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Assetic\Asset\FileAsset;

/**
 * Media
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\MediaRepository")
 * @ORM\Table(name="kuma_media")
 * @ORM\HasLifecycleCallbacks
 */
class Media extends AbstractEntity
{

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=255)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="location", nullable=true)
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="content_type")
     */
    protected $contentType;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    public $metadata;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;

    /**
     * @var Folder
     *
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="media")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id")
     */
    protected $folder;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $filesize;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->deleted = false;
    }

    /**
     * @return string
     */
    public function getFileSize()
    {
        $size = $this->filesize;
        if ($size < 1024) {
            return $size . "b";
        } else {
            $help = $size / 1024;
            if ($help < 1024) {
                return round($help, 1) . "kb";
            } else {
                return round(($help / 1024), 1) . "mb";
            }
        }
    }

    /**
     * @param int $filesize
     */
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
     * Set location
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
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
     *
     * @return Media
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
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
     * @param \DateTime $createdAt
     *
     * @param Media
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Media
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set content
     *
     * @param mixed $content
     *
     * @return Media
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->setUpdatedAt(new \DateTime());

        return $this;
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
     * Set folder
     *
     * @param Folder $folder
     *
     * @return Media
     */
    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->show();
    }


    /**
     * @param string $format  format
     * @param array  $options options
     *
     * @return string
     */
    public function show($format = null, $options = array())
    {
        $path = "/";
        $path = $path . $this->getUuid();
        if (isset($format)) {
            $path = $path . "_" . $format;
        }
        $path = $path . "." . ExtensionGuesser::getInstance()->guess($this->getContentType());

        return $path;
    }

    /**
     * @return string
     */
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