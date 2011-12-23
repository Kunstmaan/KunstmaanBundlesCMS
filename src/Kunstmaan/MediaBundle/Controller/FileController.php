<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\MediaType;
use Kunstmaan\MediaBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * file controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class FileController extends Controller
{

    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_file_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:FileGallery')->getFileGallery($gallery_id, $em);

        $request = $this->getRequest();
        $helper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                if ($helper->getMedia()!=null) {
                    $file = new File();
                    $file->setName($helper->getMedia()->getClientOriginalName());
                    $file->setContent($helper->getMedia());
                    $file->setGallery($gallery);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($file, $em);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Gallery')
                       ->getAllGalleriesByType();

        return array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/{media_id}/edit", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_file_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
 /*   public function editAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $file = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);

        $request = $this->getRequest();
        $picturehelper = new MediaHelper();
        $form = $this->createForm(new MediaType(), $picturehelper);

        $gallery = $file->getGallery();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                if ($picturehelper->getMedia()!=null) {
                    $file->setName($picturehelper->getMedia()->getClientOriginalName());
                    $file->setContent($picturehelper->getMedia());
                    $file->setGallery($gallery);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($file, $em);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                        ->getAllGalleries();

        return array(
            'media' => $file,
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }*/
}