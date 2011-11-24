<?php
// src/Kunstmaan/KAdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;

/**
 * picture controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class SlideController extends Controller
{

    public function showAction($media_id, $format = null, array $options = array())
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Slide', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                                ->getAllGalleries();

        $picturehelper = new Slide();
        $form = $this->createForm(new SlideType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:Slide:show.html.twig', array(
                    'form' => $form->createView(),
                    'media' => $media,
                    'format' => $format,
                    'gallery' => $gallery,
                    'galleries' => $galleries
                ));
    }

    public function newAction($gallery_id)
    {
        $gallery = $this->getSlideGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();

        $picturehelper = new Slide();
        $form = $this->createForm(new SlideType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:Slide:create.html.twig', array(
            'form'   => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function createAction($gallery_id)
    {
        $gallery = $this->getSlideGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                         ->getAllGalleries();

        $request = $this->getRequest();
        $slide = new Slide();
        $slide->setGallery($gallery);
        $form = $this->createForm(new SlideType(), $slide);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($slide);
                    $em->flush();

                    //$picturehelp = $this->getPicture($picture->getId());
                    return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                                   'gallery' => $gallery,
                                   'galleries' => $galleries
                    ));
                }
            }

        return $this->render('KunstmaanMediaBundle:Slide:create.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    protected function getSlide($picture_id){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $picture = $em->getRepository('KunstmaanMediaBundle:Slide')->find($picture_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find slides.');
        }

        return $picture;
    }

    protected function getSlideGallery($gallery_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:SlideGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find slide gallery.');
        }

        return $imagegallery;
    }



}