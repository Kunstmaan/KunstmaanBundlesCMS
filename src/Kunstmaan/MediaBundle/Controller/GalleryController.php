<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
abstract class GalleryController extends Controller
{

    abstract function showAction($id, $slug);

    public function parentnewAction($gallery){
        $form = $this->createForm($gallery->getFormType(), $gallery);

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                               ->getAllGalleries();

        return $this->render('KunstmaanMediaBundle:Gallery:create.html.twig', array(
            'gallery' => $gallery,
            'form'   => $form->createView(),
            'galleries'     => $galleries
        ));
    }

    public function parentsubnewAction($gallery, $id){
          $em = $this->getDoctrine()->getEntityManager();
           $parent = $em->find($gallery->getStrategy()->getGalleryClassName(), $id);

           $gallery->setParent($parent);
           $form = $this->createForm(new SubGalleryType(), $gallery);

           $em = $this->getDoctrine()->getEntityManager();
           $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                  ->getAllGalleries();

           return $this->render('KunstmaanMediaBundle:Gallery:subcreate.html.twig', array(
               'form'   => $form->createView(),
               'galleries'     => $galleries,
               'parent' => $parent
           ));
       }

    public function parentcreateAction($gallery){
        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType(), $gallery);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($gallery);
                $em->flush();

                $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                               ->getAllGalleries();

                return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                          'gallery' => $gallery,
                          'galleries'     => $galleries,
                ));
            }
        }

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                       ->getAllGalleries();

        return $this->render('KunstmaanMediaBundle:Gallery:create.html.twig', array(
            'gallery' => $gallery,
            'form' => $form->createView(),
            'galleries'     => $galleries
        ));
    }

    public function parentsubcreateAction($gallery,$id){
            $request = $this->getRequest();

            $em = $this->getDoctrine()->getEntityManager();
            $parent = $em->find($gallery->getStrategy()->getGalleryClassName(), $id);

            $gallery->setParent($parent);
            $form = $this->createForm(new SubGalleryType(), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()){
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($gallery);
                    $em->flush();

                    $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                                   ->getAllGalleries();

                    return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                              'gallery' => $gallery,
                              'galleries'     => $galleries,
                    ));
                }
            }

            $em = $this->getDoctrine()->getEntityManager();
            $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                           ->getAllGalleries();

            return $this->render('KunstmaanMediaBundle:Gallery:subcreate.html.twig', array(
                'form' => $form->createView(),
                'galleries'     => $galleries,
                'parent' => $parent
            ));
        }
}