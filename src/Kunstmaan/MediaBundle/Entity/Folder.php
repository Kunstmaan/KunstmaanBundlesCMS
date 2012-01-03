<?php

namespace  Kunstmaan\MediaBundle\Entity;

use Kunstmaan\MediaBundle\Helper\FolderStrategy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a folder from the MediaBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\FolderRepository")
 * @ORM\Table(name="media_folder")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({ "folder"="Folder", "gallery" = "Gallery", "imagegallery" = "ImageGallery", "filegallery" = "FileGallery", "slidegallery" = "SlideGallery" , "videogallery" = "VideoGallery"})
 * @ORM\HasLifecycleCallbacks
 */
class Folder{

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
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent")
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
     * @ORM\Column(type="boolean")
     */
	protected $candelete;
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $rel;
    
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
        $this->setCanDelete(true);
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
    
    public function setCanDelete($bool){
    	$this->candelete = $bool;
    }
    
    public function canDelete(){
    	return $this->candelete;
    }
    
    public function setRel($rel){
    	$this->rel = $rel;
    }
    
    public function getRel(){
    	return $this->rel;
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
     * @param Kunstmaan\MediaBundle\Entity\Folder $parent
     */
    public function setParent(Folder $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Kunstmaan\MediaBundle\Entity\Folder 
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
     * @param Kunstmaan\MediaBundle\Entity\Gallery $children
     */
    public function addGallery(Folder $children)
    {
        $this->children[] = $children;
    }

     /**
      * Add children
      *
      * @param \Kunstmaan\MediaBundle\Entity\ImageGallery $children
      */
     public function addChild(Folder $child)
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
     * @param Kunstmaan\MediaBundle\Entity\Media $files
     */
    public function addMedia(Media $files)
    {
        $this->files[] = $files;
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
     * @ORM\PreRemove
     */
    public function preRemove()
    {

    }

	public function hasActive($id){
		$bool = false;
		foreach($this->getChildren() as $child){
			$bool = $child->hasActive($id);
			if($bool == true) return true;
			if($child->getId()==$id) return true;	
		}
		return false;
	}
	
	public function getStrategy(){
		return new FolderStrategy();
	}
	
	public function getFormType($gallery = null)
	{
		return new \Kunstmaan\MediaBundle\Form\FolderType($this->getStrategy()->getGalleryClassName(), $gallery);
	}
	
	public function getType()
	{
		return $this->getStrategy()->getType();
	}
}