<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Kunstmaan\MediaBundle\Entity\SlideGallery;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class SlideGalleryController extends GalleryController
{

    public function showAction($id, $slug)
    {
        return $this->parentshowAction($id, $slug, new \Kunstmaan\MediaBundle\Entity\Slide(), new \Kunstmaan\MediaBundle\Form\SlideType(), new SlideGallery());

    }

    public function editAction($id){
            $em = $this->getDoctrine()->getEntityManager();
            $gallery = $em->find('Kunstmaan\MediaBundle\Entity\SlideGallery', $id);

            return $this->parenteditAction($gallery);
        }

        public function updateAction($id){
            $em = $this->getDoctrine()->getEntityManager();
                    $gallery = $em->find('Kunstmaan\MediaBundle\Entity\SlideGallery', $id);
              return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Entity\Slide(), new \Kunstmaan\MediaBundle\Form\SlideType());
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
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Entity\Slide(), new \Kunstmaan\MediaBundle\Form\SlideType());
    }

    public function subcreateAction($id){
        $gallery = new SlideGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Entity\Slide(), new \Kunstmaan\MediaBundle\Form\SlideType());
    }

}