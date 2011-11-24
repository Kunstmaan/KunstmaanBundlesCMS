<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\KMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\KMediaBundle\Form\GalleryType;
use Kunstmaan\KMediaBundle\Form\SubGalleryType;
use Kunstmaan\KMediaBundle\Entity\SlideGallery;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class SlideGalleryController extends GalleryController
{

    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')->find($id);
        $galleries = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')
                        ->getAllGalleries();

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find slide gallery.');
        }

        return $this->render('KunstmaanKMediaBundle:Gallery:show.html.twig', array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
         ));
    }

    public function newAction(){
        $gallery = new SlideGallery();
        return $this->parentnewAction($gallery);
    }

    public function subnewAction($id){
        $gallery = new SlideGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    public function createAction(){
        $gallery = new SlideGallery();
        return $this->parentcreateAction($gallery);
    }

    public function subcreateAction($id){
        $gallery = new SlideGallery();
        return $this->parentsubcreateAction($gallery,$id);
    }

}