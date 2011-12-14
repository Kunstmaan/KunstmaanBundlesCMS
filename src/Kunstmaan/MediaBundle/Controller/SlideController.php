<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * picture controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class SlideController extends Controller
{
    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_slide_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($gallery_id, $em);

        $request = $this->getRequest();
        $slide = new Slide();
        $slide->setGallery($gallery);
        $form = $this->createForm(new SlideType(), $slide);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em->getRepository('KunstmaanMediaBundle:Media')->save($slide, $em);

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
        return array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/{media_id}/edit", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_slide_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $slide = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);
        $slide->setContent($slide->getUuid());
        $request = $this->getRequest();
        $form = $this->createForm(new SlideType(), $slide);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $slide->setUuid($slide->getContent());
                $em->getRepository('KunstmaanMediaBundle:Media')->save($slide, $em);

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array( 'media_id' => $slide->getId() )));
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
}