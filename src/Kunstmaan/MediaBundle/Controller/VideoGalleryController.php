<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Kunstmaan\MediaBundle\Entity\VideoGallery;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class VideoGalleryController extends GalleryController
{

    public function showAction($id, $slug)
    {
        return $this->parentshowAction($id, $slug, new \Kunstmaan\MediaBundle\Entity\Video(), new \Kunstmaan\MediaBundle\Form\VideoType(), new VideoGallery());

    }

    public function newAction(){
        $gallery = new VideoGallery();
        return $this->parentnewAction($gallery);
    }

    public function subnewAction($id){
        $gallery = new VideoGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    public function createAction(){
        $gallery = new VideoGallery();
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Entity\Video(), new \Kunstmaan\MediaBundle\Form\VideoType());
    }

    public function subcreateAction($id){
        $gallery = new VideoGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Entity\Video(), new \Kunstmaan\MediaBundle\Form\VideoType());
    }

}