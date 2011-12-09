<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * videogallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class VideoGalleryController extends GalleryController
{
    /**
     * @Route("/", name="KunstmaanMediaBundle_videogallery_new")
     */
    public function newAction(){
        $gallery = new VideoGallery();
        return $this->parentnewAction($gallery);
    }

    /**
     * @Route("/sub/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_videogallery_subnew")
     */
    public function subnewAction($id){
        $gallery = new VideoGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    /**
     * @Route("/create", name="KunstmaanMediaBundle_videogallery_create")
     * @Method({"POST"})
     */
    public function createAction(){
        $gallery = new VideoGallery();
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Entity\Video(), new \Kunstmaan\MediaBundle\Form\VideoType());
    }

    /**
     * @Route("/sub/create/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_videogallery_subcreate")
     * @Method({"POST"})
     */
    public function subcreateAction($id){
        $gallery = new VideoGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Entity\Video(), new \Kunstmaan\MediaBundle\Form\VideoType());
    }

}