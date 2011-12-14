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

        $em->remove($media);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    }

    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_video_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $this->getVideoGallery($gallery_id, $em);

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

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:VideoGallery')
                        ->getAllGalleries();

        return array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/{media_id}/edit", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_video_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $slide = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);
        $slide->setContent($slide->getUuid());
        $request = $this->getRequest();
        $form = $this->createForm(new VideoType(), $slide);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $slide->setUuid($slide->getContent());
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($slide);
                $em->flush();

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_video_show', array( 'media_id' => $slide->getId() )));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
        return array(
            'form' => $form->createView(),
            'media' => $slide,
            'gallery' => $slide->getGallery(),
            'galleries' => $galleries
        );
    }

    protected function getVideoGallery($gallery_id, \Doctrine\ORM\EntityManager $em)
    {
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:VideoGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find Video gallery.');
        }

        return $imagegallery;
    }



}