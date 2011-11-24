<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\KMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\KMediaBundle\Form\GalleryType;
use Kunstmaan\KMediaBundle\Entity\ImageGallery;
use Kunstmaan\KMediaBundle\Form\SubGalleryType;

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
        $gallery = $em->getRepository('KunstmaanKMediaBundle:ImageGallery')->find($id);
        $galleries = $em->getRepository('KunstmaanKMediaBundle:ImageGallery')
                        ->getAllGalleries();

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find image gallery.');
        }

        return $this->render('KunstmaanKMediaBundle:Gallery:show.html.twig', array(
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
        $galleries = $em->getRepository('KunstmaanKMediaBundle:ImageGallery')
                        ->getAllGalleries();

        return $this->render('KunstmaanKMediaBundle:ImageGallery:ckeditor.html.twig', array(
            'galleries'     => $galleries
        ));
    }

}