<?php
// src/Kunstmaan/KAdminBundle/controller/PictureController.php

namespace Kunstmaan\KMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\KMediaBundle\Helper\MediaHelper;
use Kunstmaan\KMediaBundle\Form\SlideType;
use Kunstmaan\KMediaBundle\Entity\Slide;

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
        $media = $em->find('\Kunstmaan\KMediaBundle\Entity\Slide', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')
                                ->getAllGalleries();

        $picturehelper = new Slide();
        $form = $this->createForm(new SlideType(), $picturehelper);

        return $this->render('KunstmaanKMediaBundle:Slide:show.html.twig', array(
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
        $galleries = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')
                        ->getAllGalleries();

        $picturehelper = new Slide();
        $form = $this->createForm(new SlideType(), $picturehelper);

        return $this->render('KunstmaanKMediaBundle:Slide:create.html.twig', array(
            'form'   => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function createAction($gallery_id)
    {
        $gallery = $this->getSlideGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')
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
                    return $this->render('KunstmaanKMediaBundle:Gallery:show.html.twig', array(
                                   'gallery' => $gallery,
                                   'galleries' => $galleries
                    ));
                }
            }

        return $this->render('KunstmaanKMediaBundle:Slide:create.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    protected function getSlide($picture_id){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $picture = $em->getRepository('KunstmaanKMediaBundle:Slide')->find($picture_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find slides.');
        }

        return $picture;
    }

    protected function getSlideGallery($gallery_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $imagegallery = $em->getRepository('KunstmaanKMediaBundle:SlideGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find slide gallery.');
        }

        return $imagegallery;
    }



}