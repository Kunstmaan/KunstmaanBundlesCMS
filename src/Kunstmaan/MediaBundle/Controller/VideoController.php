<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;

/**
 * picture controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class VideoController extends Controller
{

    public function showAction($media_id, $format = null, array $options = array())
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Video', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                                ->getAllGalleries();

        $picturehelper = new Video();
        $form = $this->createForm(new VideoType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:Video:show.html.twig', array(
                    'form' => $form->createView(),
                    'media' => $media,
                    'format' => $format,
                    'gallery' => $gallery,
                    'galleries' => $galleries
                ));
    }

    public function deleteAction($media_id)
          {
                  $em = $this->getDoctrine()->getEntityManager();
                  $media = $em->find('\Kunstmaan\MediaBundle\Entity\Media', $media_id);
                  $gallery = $media->getGallery();
                  $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                                          ->getAllGalleries();
                  $em->remove($media);
                  $em->flush();

                  $picturehelper = new Video();
                  $form = $this->createForm(new VideoType(), $picturehelper);

                  $sub = new \Kunstmaan\MediaBundle\Entity\VideoGallery();
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
        $gallery = $this->getVideoGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                        ->getAllGalleries();

        $picturehelper = new Video();
        $form = $this->createForm(new VideoType(), $picturehelper);

        return $this->render('KunstmaanMediaBundle:Video:create.html.twig', array(
            'form'   => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    public function createAction($gallery_id)
    {
        $gallery = $this->getVideoGallery($gallery_id);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                         ->getAllGalleries();

        $request = $this->getRequest();
        $Video = new Video();
        $Video->setGallery($gallery);
        $form = $this->createForm(new VideoType(), $Video);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($Video);
                    $em->flush();

                $Video = new Video();
                        $Video->setGallery($gallery);
                        $form = $this->createForm(new VideoType(), $Video);

                $sub = new \Kunstmaan\MediaBundle\Entity\VideoGallery();
                                    $sub->setParent($gallery);
                                    $subform = $this->createForm(new \Kunstmaan\MediaBundle\Form\SubGalleryType(), $sub);

                    //$picturehelp = $this->getPicture($picture->getId());
                    return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                        'form' => $form->createView(),
                        'subform' => $subform->createView(),
                        'gallery' => $gallery,
                                   'galleries' => $galleries
                    ));
                }
            }

        return $this->render('KunstmaanMediaBundle:Video:create.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        ));
    }

    protected function getVideo($picture_id){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $picture = $em->getRepository('KunstmaanMediaBundle:Video')->find($picture_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find Videos.');
        }

        return $picture;
    }

    protected function getVideoGallery($gallery_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:VideoGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find Video gallery.');
        }

        return $imagegallery;
    }



}