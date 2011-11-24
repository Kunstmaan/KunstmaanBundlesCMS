<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Form\SubGalleryType;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class ImageGalleryController extends GalleryController
{

    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:ImageGallery')->find($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find image gallery.');
        }

        return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
         ));
    }

    public function newAction(){
        $gallery = new ImageGallery();
        return $this->parentnewAction($gallery);
    }

    public function subnewAction($id){
        $gallery = new ImageGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    public function createAction(){
        $gallery = new ImageGallery();
        return $this->parentcreateAction($gallery);
    }

    public function subcreateAction($id){
        $gallery = new ImageGallery();
        return $this->parentsubcreateAction($gallery,$id);
    }

    public function ckeditorAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();

        return $this->render('KunstmaanMediaBundle:ImageGallery:ckeditor.html.twig', array(
            'galleries'     => $galleries
        ));
    }

}