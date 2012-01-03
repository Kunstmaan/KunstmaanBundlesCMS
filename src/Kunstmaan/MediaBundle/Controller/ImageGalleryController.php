<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Form\SubFolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class ImageGalleryController
{
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