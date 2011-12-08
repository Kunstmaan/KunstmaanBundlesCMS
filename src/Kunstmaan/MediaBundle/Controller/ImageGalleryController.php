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
        return $this->parentshowAction($id, $slug, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType(), new ImageGallery());

    }

    public function newAction(){
        $gallery = new ImageGallery();
        return $this->parentnewAction($gallery);
    }

    public function editAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->find('Kunstmaan\MediaBundle\Entity\ImageGallery', $id);

        return $this->parenteditAction($gallery);
    }

    public function updateAction($id){
        $em = $this->getDoctrine()->getEntityManager();
                $gallery = $em->find('Kunstmaan\MediaBundle\Entity\ImageGallery', $id);
          return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
      }

    public function subnewAction($id){
        $gallery = new ImageGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    public function createAction(){
        $gallery = new ImageGallery();
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
    }

    public function subcreateAction($id){
        $gallery = new ImageGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Helper\MediaHelper(), new \Kunstmaan\MediaBundle\Form\MediaType());
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