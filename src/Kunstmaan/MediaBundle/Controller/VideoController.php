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
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_video_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($gallery_id, $em);

        $request = $this->getRequest();
        $Video = new Video();
        $Video->setGallery($gallery);
        $form = $this->createForm(new VideoType(), $Video);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em->getRepository('KunstmaanMediaBundle:Media')->save($video, $em);

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

        $video = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);
        $video->setContent($video->getUuid());
        $request = $this->getRequest();
        $form = $this->createForm(new VideoType(), $video);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $video->setUuid($video->getContent());
                $em->getRepository('KunstmaanMediaBundle:Media')->save($video, $em);

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $video->getId() )));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
        return array(
            'form' => $form->createView(),
            'media' => $video,
            'gallery' => $video->getGallery(),
            'galleries' => $galleries
        );
    }
}