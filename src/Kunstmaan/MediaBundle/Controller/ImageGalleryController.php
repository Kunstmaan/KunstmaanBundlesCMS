<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class ImageGalleryController extends GalleryController
{
    /**
     * @Route("/create", name="KunstmaanMediaBundle_imagegallery_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(){
        $gallery = new ImageGallery();
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
    }

    /**
     * @Route("/sub/create/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_imagegallery_subcreate")
     * @Method({"GET", "POST"})
     */
    public function subcreateAction($id){
        $gallery = new ImageGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
    }

    /**
     * @Route("/ckeditor", name="KunstmaanMediaBundle_imagegallery_ckeditor")
     * @Template()
     */
    public function ckeditorAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();

        return array(
            'galleries'     => $galleries
        );
    }

}