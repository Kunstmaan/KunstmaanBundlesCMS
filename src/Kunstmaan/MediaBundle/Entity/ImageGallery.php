<?php

namespace  Kunstmaan\KMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\KMediaBundle\Repository\ImageGalleryRepository")
 * @ORM\Table(name="image_gallery")
 * @ORM\HasLifecycleCallbacks
 */
class ImageGallery extends Gallery{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $slug
     */
    protected $slug;

    /**
     * @var datetime $created
     */
    protected $created;

    /**
     * @var datetime $updated
     */
    protected $updated;

    /**
     * @var Kunstmaan\KMediaBundle\Entity\Gallery
     */
    protected $parent;

    /**
     * @var Kunstmaan\KMediaBundle\Entity\Gallery
     */
    protected $children;

    /**
     * @var Kunstmaan\KMediaBundle\Entity\Media
     */
    protected $files;

    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function addImage(File $child)
    {
        $this->files[] = $child;
        $child->setGallery($this);
    }


    public function getImages()
    {
        return $this->files;
    }

    public function setImages($children)
    {
        $this->files = $children;
    }

    /**
     * Add images
     *
     * @param Kunstmaan\KMediaBundle\Entity\File $images
     */
    public function addImages(File $images)
    {
        $this->files[] = $images;
    }

    public function getStrategy(){
        return new \Kunstmaan\KMediaBundle\Helper\ImageGalleryStrategy();
    }
    /**
     * @var string $content
     */
    protected $content;


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