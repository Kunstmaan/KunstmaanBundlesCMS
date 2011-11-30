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
class GalleryController extends Controller
{

    function parentshowAction($id, $slug, $formf, $type, $sub){
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->find($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                ->getAllGalleries();

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find file gallery.');
        }

        $form = $this->createForm($type, $formf);
        $sub->setParent($gallery);
        $subform = $this->createForm(new SubGalleryType(), $sub);

        return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                    'form'          => $form->createView(),
                    'subform'       => $subform->createView(),
                    'gallery'       => $gallery,
                    'galleries'     => $galleries
                 ));
    }

    public function deleteAction($gallery_id){
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->find('Kunstmaan\MediaBundle\Entity\Gallery', $gallery_id);

        $this->deleteFiles($gallery, $em);
        $this->deleteChildren($gallery, $em);
        $em->remove($gallery);
        $em->flush();

        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                        ->getAllGalleries();

        return $this->render('KunstmaanMediaBundle:Media:'.$gallery->getStrategy()->getType().'s.html.twig', array(
                    'gallery' => $gallery,
                    'galleries' => $galleries
                ));
    }

    public function deleteFiles(\Kunstmaan\MediaBundle\Entity\Gallery $gallery, \Doctrine\ORM\EntityManager $em){
        foreach($gallery->getFiles() as $file){
            //$fullpath = $file->getFullPath();
            //unlink($fullpath);
            $em->remove($file);
        }
    }

    public function deleteChildren(\Kunstmaan\MediaBundle\Entity\Gallery $gallery, \Doctrine\ORM\EntityManager $em){
        foreach($gallery->getChildren() as $child){
            $this->deleteFiles($child, $em);
            $this->deleteChildren($child, $em);
            $em->remove($child);
        }
     }

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
               'subform'   => $form->createView(),
               'galleries'     => $galleries,
               'gallery' => $gallery,
               'parent' => $parent
           ));
       }

    public function parentcreateAction($gallery,$formf, $type){
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

                $mediaform = $this->createForm($type, $formf);

                return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                          'form' => $mediaform->createView(),
                          'subform' => $form->createView(),
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

    public function parentsubcreateAction($gallery,$id, $formf, $type){
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

                    $mediaform = $this->createForm($type, $formf);

                    return $this->render('KunstmaanMediaBundle:Gallery:show.html.twig', array(
                              'form' => $mediaform->createView(),
                              'subform' => $form->createView(),
                              'gallery' => $gallery,
                              'galleries'     => $galleries,
                    ));
                }
            }

            $em = $this->getDoctrine()->getEntityManager();
            $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                           ->getAllGalleries();

            return $this->render('KunstmaanMediaBundle:Gallery:subcreate.html.twig', array(
                'subform' => $form->createView(),
                'galleries' => $galleries,
                'gallery' => $gallery,
                'parent' => $parent
            ));
        }
}