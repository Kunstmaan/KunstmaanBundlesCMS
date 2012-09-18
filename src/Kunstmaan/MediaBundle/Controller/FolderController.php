<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Kunstmaan\AdminListBundle\AdminList\AdminList;

use Kunstmaan\MediaBundle\Helper\FolderFactory;
use Kunstmaan\MediaBundle\Entity\Video;
use Kunstmaan\MediaBundle\Entity\Slide;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\File;
use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @param int $folderId The folder id
     *
     * @Route("/{folderId}/{slug}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_show")
     * @Template()
     *
     * @return array
     */
    public function showAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        $itemList = "";
        $listConfigurator = $folder->getStrategy()->getListConfigurator($folder);
        if (isset($listConfigurator) && $listConfigurator != null) {
            /* @var AdminList $itemList */
            $itemList = $this->get("adminlist.factory")->createList($listConfigurator, $em, array("gallery" => $folder->getId()));
            $itemList->bindRequest($this->getRequest());
        }
        $sub = $folder->getStrategy()->getNewGallery();
        $sub->setParent($folder);
        $subForm = $this->createForm(new SubFolderType(), $sub);
        $editForm = $this->createForm($folder->getFormType($folder), $folder);

        return array(
            'subform'       => $subForm->createView(),
            'editform'      => $editForm->createView(),
            'gallery'       => $folder,
            'galleries'     => $galleries,
            'itemlist'      => $itemList
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("/delete/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_delete")
     *
     * @return RedirectResponse
     */
    public function deleteAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $parentGallery = $folder->getParent();

        $em->getRepository('KunstmaanMediaBundle:Folder')->delete($folder);

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $parentGallery->getId(),
            'slug' => $parentGallery->getSlug()
        )));
    }

    /**
     * @param int $folderId
     *
     * @Route("/update/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $form = $this->createForm($folder->getFormType($folder), $folder);
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId(), 'slug' => $folder->getSlug())));
            }
        }
        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return array(
            'gallery' => $folder,
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
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = FolderFactory::getTypeFolder($type);
        $form = $this->createForm(new FolderType($folder->getStrategy()->getGalleryClassName()), $folder);
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId(), 'slug' => $folder->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return $this->render('KunstmaanMediaBundle:Folder:create.html.twig', array(
            'gallery' => $folder,
            'form' => $form->createView(),
            'galleries'     => $galleries
        ));
    }

    /**
     * @param int $folderId
     *
     * @Route("/subcreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_sub_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return Response
     */
    public function subCreateAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $parent */
        $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $folder = $parent->getStrategy()->getNewGallery();
        $folder->setParent($parent);
        $form = $this->createForm(new SubFolderType(), $folder);
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getRepository('KunstmaanMediaBundle:Folder')->save($folder);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId' => $folder->getId(), 'slug' => $folder->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFoldersByType();

        return $this->render('KunstmaanMediaBundle:Folder:subcreate.html.twig', array(
            'subform' => $form->createView(),
            'galleries' => $galleries,
            'gallery' => $folder,
            'parent' => $parent
        ));
    }

    /**
     * @Route("/movenodes", name="KunstmaanMediaBundle_folder_movenodes")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function moveNodesAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        /* @var string $parentid */
        $parentid = $request->get('parentid');
        /* @var Folder $parent */
        $parent = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($parentid);

        /* @var int $fromposition */
        $fromposition = $request->get('fromposition');
        /* @var int $afterposition */
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