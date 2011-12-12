<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

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
     * @Route("/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_file_show")
     * @Template()
     */
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

        return array(
            'media' => $media,
            'gallery' => $gallery,
            'galleries' => $galleries,
            'form' => $form->createView(),
            'subform' => $subform->createView()
       );
    }

    /**
     * @Route("/delete/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_file_delete")
     */
    public function deleteAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\File', $media_id);
        $gallery = $media->getGallery();
        $em->remove($media);
        $em->flush();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    }

    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_file_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $gallery = $this->getFileGallery($gallery_id, $em);

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

                    $em->persist($picture);
                    $em->flush();

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:FileGallery')
                        ->getAllGalleries();

        return array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    protected function getFileGallery($gallery_id, \Doctrine\ORM\EntityManager $em)
    {
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:FileGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find file gallery.');
        }

        return $imagegallery;
    }



}