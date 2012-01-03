<?php

namespace  Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\MediaRepository")
 * @ORM\Table(name="media")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"media" = "Media", "image" = "Image", "file" = "File", "slide" = "Slide" , "video" = "Video"})
 * @ORM\HasLifecycleCallbacks
 */
abstract class Media{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
    protected $context;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $classtype;

    public function __construct($context = null)
    {
        if (null !== $context) {
            $this->setContext($context);
        }
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
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

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set context
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    public function show($format=null, $options = array())
    {
        $path = $this->getContext()."/";
        $path = $path.$this->getUuid();
        if(isset($format)){
            $path = $path."_".$format;
        }
        $path = $path.".".\Kunstmaan\MediaBundle\Helper\Generator\ExtensionGuesser::guess($this->getContentType());
        return $path;
    }

    public function getClassType(){
    	return $this->classtype;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}