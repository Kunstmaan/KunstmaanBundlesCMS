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
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:FileGallery')->find($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                        ->getAllGalleries();

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find file gallery.');
        }

        return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
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