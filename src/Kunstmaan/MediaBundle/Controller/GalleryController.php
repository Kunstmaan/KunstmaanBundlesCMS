<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class GalleryController extends Controller
{
    /**
     * @Route("/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_gallery_show")
     * @Template()
     */
    function showAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->find($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                ->getAllGalleries();

        $adminlist = $this->get("adminlist.factory")->createList(new \Kunstmaan\MediaBundle\Helper\MediaList\FileListConfigurator(), $em);
        $adminlist->bindRequest($this->getRequest());

        if (!$gallery) {
            throw $this->createNotFoundException('Unable to find file gallery.');
        }

        $form = $this->createForm($gallery->getStrategy()->getFormType(), $gallery->getStrategy()->getFormHelper());
        $sub = $gallery->getStrategy()->getNewGallery();
        $sub->setParent($gallery);
        $subform = $this->createForm(new SubGalleryType(), $sub);

        $editform = $this->createForm($gallery->getFormType($gallery), $gallery);

        return array(
            'form'          => $form->createView(),
            'subform'       => $subform->createView(),
            'editform'      => $editform->createView(),
            'gallery'       => $gallery,
            'galleries'     => $galleries,
            'filelist'      => $adminlist
        );
    }

    /**
     * @Route("/delete/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_gallery_delete")
     */
    public function deleteAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->find('Kunstmaan\MediaBundle\Entity\Gallery', $gallery_id);

        $this->deleteFiles($gallery, $em);
        $this->deleteChildren($gallery, $em);
        $em->remove($gallery);
        $em->flush();

        $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                        ->getAllGalleries();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_'.$gallery->getStrategy()->getType().'s'));
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

    /**
     * @Route("/update/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_gallery_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->find('Kunstmaan\MediaBundle\Entity\Gallery', $gallery_id);
        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType($gallery), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()){
                    $em->persist($gallery);
                    $em->flush();

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }

            $galleries = $em->getRepository('KunstmaanMediaBundle:'.$gallery->getStrategy()->getName())
                                           ->getAllGalleries();

            return array(
                'gallery' => $gallery,
                'form' => $form->createView(),
                'galleries'     => $galleries
            );
     }

    public function parentcreateAction($gallery)
    {
        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType(), $gallery);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($gallery);
                $em->flush();

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
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

    public function parentsubcreateAction($gallery,$id)
    {
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

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
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