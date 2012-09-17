<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Kunstmaan\MediaBundle\Helper\FolderFactory;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\File;
use Kunstmaan\MediaBundle\Form\MediaType;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Form\SubFolderType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * folder controller.
 *
 */
class FolderController extends Controller
{
    /**
     * @param int $id
     *
     * @Route("/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     * @Template()
     *
     * @return array
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        $itemlist = "";
        $listconfigurator = $gallery->getStrategy()->getListConfigurator($gallery);
        if (isset($listconfigurator) && $listconfigurator != null) {
            $itemlist = $this->get("kunstmaan_adminlist.factory")->createList($listconfigurator, $em, array("gallery" => $gallery->getId()));
            $itemlist->bindRequest($this->getRequest());
        }

        $sub = $gallery->getStrategy()->getNewGallery($em);
        $sub->setParent($gallery);
        $subform = $this->createForm(new SubFolderType(), $sub);
        $editform = $this->createForm($gallery->getFormType($gallery), $gallery);

        return array(
            'subform'       => $subform->createView(),
            'editform'      => $editform->createView(),
            'gallery'       => $gallery,
            'galleries'     => $galleries,
            'itemlist'      => $itemlist
        );
    }

    /**
     * @param int $galleryId
     *
     * @Route("/delete/{galleryId}", requirements={"galleryId" = "\d+"}, name="KunstmaanMediaBundle_folder_delete")
     *
     * @return RedirectResponse
     */
    public function deleteAction($galleryId)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($galleryId);
        $parentGallery = $gallery->getParent();

        $em->getRepository('KunstmaanMediaBundle:Folder')->delete($gallery);

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id'  => $parentGallery->getId(),
            'slug' => $parentGallery->getSlug()
        )));
    }

    /**
     * @param int $galleryId
     *
     * @Route("/update/{galleryId}", requirements={"galleryId" = "\d+"}, name="KunstmaanMediaBundle_folder_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editAction($galleryId)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($galleryId);

        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType($gallery), $gallery);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($gallery);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                       ->getAllFoldersByType();

        return array(
            'gallery' => $gallery,
            'form' => $form->createView(),
            'galleries'     => $galleries
        );
    }

    /**
     * @param string $type
     *
     * @Route("{type}/create", name="KunstmaanMediaBundle_folder_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return Response
     */
    public function createAction($type)
    {
        $gallery = FolderFactory::getTypeFolder($type);
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $form = $this->createForm(new FolderType($gallery->getStrategy()->getGalleryClassName()), $gallery);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($gallery);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                       ->getAllFoldersByType();

        return $this->render('KunstmaanMediaBundle:Folder:create.html.twig', array(
            'gallery' => $gallery,
            'form' => $form->createView(),
            'galleries'     => $galleries
        ));
    }

    /**
     * @param int $id
     *
     * @Route("/subcreate/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_folder_subcreate")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return Response
     */
    public function subcreateAction($id)
    {
            $request = $this->getRequest();

            $em = $this->getDoctrine()->getManager();
            $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($id);

            $gallery = $parent->getStrategy()->getNewGallery($em);
            $gallery->setParent($parent);
            $form = $this->createForm(new SubFolderType(), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bind($request);
                if ($form->isValid()) {
                    $em->getRepository('KunstmaanMediaBundle:Folder')->save($gallery);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }

            $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
                                           ->getAllFoldersByType();

            return $this->render('KunstmaanMediaBundle:Folder:subcreate.html.twig', array(
                'subform' => $form->createView(),
                'galleries' => $galleries,
                'gallery' => $gallery,
                'parent' => $parent
            ));
    }

    /**
     * @Route("/movenodes", name="KunstmaanMediaBundle_folder_movenodes")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function movenodesAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $parentid = $request->get('parentid');
        $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($parentid);

        $fromposition = $request->get('fromposition');
        $afterposition = $request->get('afterposition');

        foreach ($parent->getChildren() as $child) {
            if ($child->getSequencenumber() == $fromposition) {
                if ($child->getSequencenumber() > $afterposition) {
                    $child->setSequencenumber($afterposition + 1);
                    $em->persist($child);
                } else {
                    $child->setSequencenumber($afterposition);
                    $em->persist($child);
                }
            } else {
                if ($child->getSequencenumber() > $fromposition && $child->getSequencenumber() <= $afterposition) {
                    $newpos = $child->getSequencenumber()-1;
                    $child->setSequencenumber($newpos);
                    $em->persist($child);
                } else {
                    if ($child->getSequencenumber() < $fromposition && $child->getSequencenumber() > $afterposition) {
                        $newpos = $child->getSequencenumber()+1;
                        $child->setSequencenumber($newpos);
                        $em->persist($child);
                    }
                }
            }
            $em->flush();
        }

        return array("success" => true);
    }
}