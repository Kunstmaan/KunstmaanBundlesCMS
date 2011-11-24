<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\KMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\KMediaBundle\Form\GalleryType;
use Kunstmaan\KMediaBundle\Entity\FileGallery;
use Kunstmaan\KMediaBundle\Form\SubGalleryType;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class FileGalleryController extends GalleryController
{

    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanKMediaBundle:FileGallery')->find($id);
        $galleries = $em->getRepository('KunstmaanKMediaBundle:FileGallery')
                        ->getAllGalleries();

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find file gallery.');
        }

        return $this->render('KunstmaanKMediaBundle:Gallery:show.html.twig', array(
            'gallery'       => $gallery,
            'galleries'     => $galleries
         ));
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
        return $this->parentcreateAction($gallery);
    }

    public function subcreateAction($id){
        $gallery = new FileGallery();
        return $this->parentsubcreateAction($gallery,$id);
    }

}