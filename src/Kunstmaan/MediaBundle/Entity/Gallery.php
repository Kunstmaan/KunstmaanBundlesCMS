<?php

namespace  Kunstmaan\KMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\KMediaBundle\Repository\GalleryRepository")
 * @ORM\Table(name="gallery")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({ "gallery" = "Gallery" , "imagegallery" = "ImageGallery", "filegallery" = "FileGallery", "slidegallery" = "SlideGallery" })
 * @ORM\HasLifecycleCallbacks
 */
abstract class Gallery{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="gallery")
     */
    protected $files;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $content;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function setName($name){
        $this->name = $name;
        $this->setSlug($this->name);
    }

    public function getName(){
        return $this->name;
    }

    public function slugify($text)
    {
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text); // replace non letter or digits by -
        $text = trim($text, '-'); //trim

        // transliterate
        if (function_exists('iconv')){
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        $text = strtolower($text); // lowercase
        $text = preg_replace('#[^-\w]+#', '', $text); // remove unwanted characters

        if (empty($text)){
            return 'n-a';
        }

        return $text;
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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
      * Set created
      *
      * @param datetime $created
      */
     public function setCreated($created)
     {
         $this->created = $created;
     }

     /**
      * Get created
      *
      * @return datetime
      */
     public function getCreated()
     {
         return $this->created;
     }

     /**
      * Set updated
      *
      * @param datetime $updated
      */
     public function setUpdated($updated)
     {
         $this->updated = $updated;
     }

     /**
      * Get updated
      *
      * @return datetime
      */
     public function getUpdated()
     {
         return $this->updated;
     }

    /**
     * Set parent
     *
     * @param Kunstmaan\KMediaBundle\Entity\Gallery $parent
     */
    public function setParent(Gallery $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Kunstmaan\KMediaBundle\Entity\Gallery 
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getParents()
    {
        $parent = $this->getParent();
        $parents=array();
        while($parent!=null){
            $parents[] = $parent;
            $parent = $parent->getParent();
        }
        return array_reverse($parents);
    }

    /**
     * Add children
     *
     * @param Kunstmaan\KMediaBundle\Entity\Gallery $children
     */
    public function addGallery(Gallery $children)
    {
        $this->children[] = $children;
    }

     /**
      * Add children
      *
      * @param \Kunstmaan\KMediaBundle\Entity\ImageGallery $children
      */
     public function addChild(FileGallery $child)
     {
         $this->children[] = $child;
         $child->setParent($this);
     }


     public function getChildren()
     {
         return $this->children;
     }

     public function setChildren($children)
     {
         $this->children = $children;
     }

     public function disableChildrenLazyLoading()
     {
         if (is_object($this->children)) {
             $this->children->setInitialized(true);
         }
     }

    /**
     * Add files
     *
     * @param Kunstmaan\KMediaBundle\Entity\Media $files
     */
    public function addMedia(Media $files)
    {
        $this->files[] = $files;
    }

    /**
     * Get files
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->files;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get files
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    abstract function getStrategy();

    public function getFormType()
    {
        return new \Kunstmaan\KMediaBundle\Form\GalleryType($this->getStrategy()->getGalleryClassName());
    }

    public function getType()
        {
            return $this->getStrategy()->getType();
        }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setSlug($this->slugify($this->getName()));
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * Set content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}