<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Form\MediaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * image controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class ImageController extends Controller
{
    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_image_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($gallery_id, $em);

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

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($picture, $em);

                    return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                         ->getAllGalleries();
        
        return array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }
}