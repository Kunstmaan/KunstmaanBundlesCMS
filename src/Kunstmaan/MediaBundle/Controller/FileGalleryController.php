<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Form\SubGalleryType;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class FileGalleryController extends GalleryController
{

    public function showAction($id, $slug)
    {
        return $this->parentshowAction($id, $slug, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType(), new FileGallery());
    }

    public function newAction(){
        $gallery = new FileGallery();
        return $this->parentnewAction($gallery);
    }

    public function subnewAction($id){
        $gallery = new FileGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    public function createAction(){
        $gallery = new FileGallery();
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
    }

    public function subcreateAction($id){
        $gallery = new FileGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
    }

}