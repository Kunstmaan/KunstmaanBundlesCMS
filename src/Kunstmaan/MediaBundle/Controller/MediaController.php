<?php

namespace Kunstmaan\KMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\KMediaBundle\Entity\ImageGallery;
use Kunstmaan\KMediaBundle\Entity\FileGallery;
use Kunstmaan\KMediaBundle\Entity\SlideGallery;


class MediaController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('KunstmaanKMediaBundle:Media:index.html.twig', array());
    }

    public function imagesAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanKMediaBundle:ImageGallery')
                        ->getAllGalleries();
        $gallery = new ImageGallery();
        return $this->render('KunstmaanKMediaBundle:Media:images.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function videosAction()
    {
        return $this->render('KunstmaanKMediaBundle:Media:videos.html.twig', array());
    }

    public function slidesAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')
                        ->getAllGalleries();
        $gallery = new SlideGallery();

        return $this->render('KunstmaanKMediaBundle:Media:images.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function filesAction()
    {
        $em = $this->getDoctrine()
                           ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanKMediaBundle:FileGallery')
                                ->getAllGalleries();
        $gallery = new FileGallery();

        return $this->render('KunstmaanKMediaBundle:Media:files.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }
}
