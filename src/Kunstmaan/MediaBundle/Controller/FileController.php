<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\MediaType;
use Kunstmaan\MediaBundle\Entity\File;

/**
 * picture controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class FileController extends Controller
{

    public function showAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\File', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                                ->getAllGalleries();

        $picturehelper = new MediaHelper();
                            $form = $this->createForm(new MediaType(), $picturehelper);

                            $sub = new \Kunstmaan\MediaBundle\Entity\FileGallery();
                            $sub->setParent($gallery);
                            $subform = $this->createForm(new \Kunstmaan\MediaBundle\Form\SubGalleryType(), $sub);

       return $this->render('KunstmaanMediaBundle:File:show.html.twig', array(
                    'media' => $media,
                    'gallery' => $gallery,
                    'galleries' => $galleries,
                    'form' => $form->createView(),
                    'subform' => $subform->createView()
                ));
    }

    public function deleteAction($media_id)
    {
            $em = $this->getDoctrine()->getEntityManager();
            $media = $em->find('\Kunstmaan\MediaBundle\Entity\File', $media_id);
            $gallery = $media->getGallery();
            $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                                    ->getAllGalleries();
            $em->remove($media);
            $em->flush();

            $picturehelper = new MediaHelper();
            $form = $this->createForm(new MediaType(), $picturehelper);

            $sub = new \Kunstmaan\MediaBundle\Entity\FileGallery();
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
        $gallery = $this->getFileGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                        ->getAllGalleries();

        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:File:create.html.twig', array(
            'form'   => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function createAction($gallery_id)
    {
        $gallery = $this->getFileGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                         ->getAllGalleries();

        $request = $this->getRequest();
        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                if ($picturehelper->getMedia()!=null) {
                    $picture = new File();
                    $picture->setName($picturehelper->getMedia()->getClientOriginalName());
                    $picture->setContent($picturehelper->getMedia());
                    $picture->setGallery($gallery);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($picture);
                    $em->flush();

                    $picturehelper = new MediaHelper();
                    $form = $this->createForm(new MediaType(), $picturehelper);

                    $sub = new \Kunstmaan\MediaBundle\Entity\FileGallery();
                    $sub->setParent($gallery);
                    $subform = $this->createForm(new \Kunstmaan\MediaBundle\Form\SubGalleryType(), $sub);

                    //$picturehelp = $this->getPicture($picture->getId());
                    return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                                   'gallery' => $gallery,
                                   'galleries' => $galleries,
                                   'form' => $form->createView(),
                                   'subform' => $subform->createView()
                                   // 'picture' => $picturehelp
                    ));
                }
            }
        }
        return $this->render('KunstmaanMediaBundle:File:create.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    protected function getFile($picture_id){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $picture = $em->getRepository('KunstmaanMediaBundle:File')->find($picture_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find file.');
        }

        return $picture;
    }

    protected function getFileGallery($gallery_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:FileGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find file gallery.');
        }

        return $imagegallery;
    }



}