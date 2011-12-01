<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;


class MediaController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('KunstmaanMediaBundle:Media:index.html.twig', array());
    }

    public function imagesAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();
        $gallery = new ImageGallery();
        return $this->render('KunstmaanMediaBundle:Media:images.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function videosAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                        ->getAllGalleries();
        $gallery = new VideoGallery();

        return $this->render('KunstmaanMediaBundle:Media:videos.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function slidesAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
        $gallery = new SlideGallery();

        return $this->render('KunstmaanMediaBundle:Media:slides.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function filesAction()
    {
        $em = $this->getDoctrine()
                           ->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                                ->getAllGalleries();
        $gallery = new FileGallery();

        return $this->render('KunstmaanMediaBundle:Media:files.html.twig', array(
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function parentShowAction($media_id){
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Image', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                        ->getAllGalleries();

        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:Image:show.html.twig', array(
            'form' => $form->createView(),
            'media' => $media,
            'format' => $format,
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }
}
