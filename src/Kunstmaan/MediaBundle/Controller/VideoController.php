<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * picture controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class VideoController extends Controller
{
    /**
     * @Route("/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_video_show")
     * @Template()
     */
    public function showAction($media_id, $format = null, array $options = array())
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Video', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                                ->getAllGalleries();

        $picturehelper = new Video();
        $form = $this->createForm(new VideoType(), $picturehelper);

        return array(
                    'form' => $form->createView(),
                    'media' => $media,
                    'format' => $format,
                    'gallery' => $gallery,
                    'galleries' => $galleries
               );
    }

    /**
     * @Route("/delete/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_video_delete")
     */
    public function deleteAction($media_id)
          {
                  $em = $this->getDoctrine()->getEntityManager();
                  $media = $em->find('\Kunstmaan\MediaBundle\Entity\Media', $media_id);
                  $gallery = $media->getGallery();
                  $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                                          ->getAllGalleries();
                  $em->remove($media);
                  $em->flush();


                      return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
           }

    /**
     * @Route("/{gallery_id}/new", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_video_new")
     */
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

    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_video_create")
     * @Method({"POST"})
     */
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