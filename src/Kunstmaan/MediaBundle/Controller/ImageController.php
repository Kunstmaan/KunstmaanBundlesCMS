<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Form\MediaType;

/**
 * image controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class ImageController extends Controller
{

    public function showAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Media', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                ->getAllGalleries();

        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:'.ucfirst($gallery->getStrategy()->getType()).':show.html.twig', array(
                    'form' => $form->createView(),
                    'media' => $media,
                    'gallery' => $gallery,
                    'galleries' => $galleries
                ));
    }

    public function deleteAction($media_id)
        {
                $em = $this->getDoctrine()->getEntityManager();
                $media = $em->find('\Kunstmaan\MediaBundle\Entity\Media', $media_id);
                $gallery = $media->getGallery();
                $galleries = $em->getRepository('KunstmaanMediaBundle:ImageGallery')
                                        ->getAllGalleries();
                $em->remove($media);
                $em->flush();

                $picturehelper = new MediaHelper();
                $form = $this->createForm(new MediaType(), $picturehelper);

                $sub = new \Kunstmaan\MediaBundle\Entity\ImageGallery();
                $sub->setParent($gallery);
                $subform = $this->createForm(new \Kunstmaan\MediaBundle\Form\SubGalleryType(), $sub);

                return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                            'gallery' => $gallery,
                            'galleries' => $galleries,
                            'form' => $form->createView(),
                            'subform' => $subform->createView()
                        ));
         }

    public function newAction($gallery_id)
    {
        $gallery = $this->getGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                        ->getAllGalleries();

        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:'.ucfirst($gallery->getStrategy()->getType()).':create.html.twig', array(
            'form'   => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function createAction($gallery_id)
    {
        $gallery = $this->getGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                         ->getAllGalleries();

        $request = $this->getRequest();
        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                if ($picturehelper->getMedia()!=null) {
                    $picture = new Image();
                    $picture->setName($picturehelper->getMedia()->getClientOriginalName());
                    $picture->setContent($picturehelper->getMedia());
                    $picture->setGallery($gallery);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($picture);
                    $em->flush();

                    $picturehelper = new MediaHelper();
                            $form = $this->createForm(new MediaType(), $picturehelper);

                    $sub = new \Kunstmaan\MediaBundle\Entity\ImageGallery();
                                        $sub->setParent($gallery);
                                        $subform = $this->createForm(new \Kunstmaan\MediaBundle\Form\SubGalleryType(), $sub);

                    //$picturehelp = $this->getPicture($picture->getId());
                    return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                                   'form' => $form->createView(),
                                   'subform' => $subform->createView(),
                                   'gallery' => $gallery,
                                   'galleries' => $galleries
                                   // 'picture' => $picturehelp
                    ));
                }
            }
        }
        return $this->render('KunstmaanMediaBundle:'.ucfirst($gallery->getStrategy()->getType()).':create.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    protected function getMedia($media_id){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $picture = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find media item.');
        }

        return $picture;
    }

    protected function getGallery($gallery_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find gallery.');
        }

        return $imagegallery;
    }



}