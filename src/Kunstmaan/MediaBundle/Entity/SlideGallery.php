<?php

namespace  Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Helper\SlideGalleryStrategy;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\MediaBundle\Repository\SlideGalleryRepository")
 * @ORM\Table(name="slide_gallery")
 * @ORM\HasLifecycleCallbacks
 */
class SlideGallery extends Gallery{

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
     * @var Kunstmaan\MediaBundle\Entity\Gallery
     */
    protected $parent;

    /**
     * @var Kunstmaan\MediaBundle\Entity\Gallery
     */
    protected $children;

    /**
     * @var Kunstmaan\MediaBundle\Entity\Media
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

    public function addSlide(Slide $child)
    {
        $this->files[] = $child;
        $child->setGallery($this);
    }


    public function getSlides()
    {
        return $this->files;
    }

    public function setSlides($children)
    {
        $this->files = $children;
    }

    /**
     * Add images
     *
     * @param Kunstmaan\MediaBundle\Entity\File $images
     */
    public function addSlides(Slide $images)
    {
        $this->files[] = $images;
    }

    public function getStrategy(){
        return new SlideGalleryStrategy();
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