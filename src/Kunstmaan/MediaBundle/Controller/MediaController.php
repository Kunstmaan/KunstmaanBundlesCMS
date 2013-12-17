<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Kunstmaan\MediaBundle\Form\BulkUploadType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\BulkUploadHelper;
use Kunstmaan\MediaBundle\Entity\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Kunstmaan\MediaBundle\Helper\MediaManager;

/**
 * MediaController
 */
class MediaController extends Controller
{

    /**
     * @param int $mediaId
     *
     * @Route("/{mediaId}", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_show")
     *
     * @return Response
     */
    public function showAction($mediaId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Media $media */
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $folder = $media->getFolder();

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $handler = $mediaManager->getHandler($media);
        $helper = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $media = $helper->getMedia();
                $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', array('mediaId'  => $media->getId())));
            }
        }
        $showTemplate = $mediaManager->getHandler($media)->getShowTemplate($media);

        return $this->render($showTemplate, array(
                'handler' => $handler,
                'mediamanager' => $this->get('kunstmaan_media.media_manager'),
                'editform'      => $form->createView(),
                'media' => $media,
                'helper' => $helper,
                'folder' => $folder));
    }

    /**
     * @param int $mediaId
     *
     * @Route("/delete/{mediaId}", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_delete")
     *
     * @return RedirectResponse
     */
    public function deleteAction($mediaId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Media $media */
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $medianame = $media->getName();
        $folder = $media->getFolder();

        $em->getRepository('KunstmaanMediaBundle:Media')->delete($media);

        $this->get('session')->getFlashBag()->add('success', 'Entry \''.$medianame.'\' has been deleted!');

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId())));
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkupload/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_bulk_upload")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function bulkUploadAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $request = $this->getRequest();
        $helper  = new BulkUploadHelper();

        $form = $this->createForm(new BulkUploadType('*/*'), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $mediaAdded = false;
                
                foreach ($helper->getFiles() as $file) {
                    if (!is_null($file)) {
                        /* @var Media $media */
                        $media = $this->get('kunstmaan_media.media_manager')->getHandler($file)->createNew($file);
                        $media->setFolder($folder);
                        $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                        $mediaAdded = true;
                    }
                }

                if ($mediaAdded) {
                    $this->get('session')->getFlashBag()->add('success', 'New entry has been uploaded');

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId())));
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'Please select at least one file');
                }
            }
        }

        $formView = $form->createView();
        $filesfield = $formView->children['files'];
        $filesfield->vars = array_replace($filesfield->vars, array(
            'full_name' => 'kunstmaan_mediabundle_bulkupload[files][]'
        ));

        return array(
            'form'      => $formView,
            'folder'   => $folder
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("drop/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_drop_upload")
     * @Method({"GET", "POST"})
     *
     * @return array|RedirectResponse
     */
    public function dropAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $drop = null;
        if (array_key_exists('files', $_FILES) && $_FILES['files']['error'] == 0 ) {
            $drop = $this->getRequest()->files->get('files');
        } else {
            $drop = $this->getRequest()->get('text');
        }
        $media = $this->get('kunstmaan_media.media_manager')->createNew($drop);
        if ($media) {
            $media->setFolder($folder);
            $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

            return new Response(json_encode(array('status'=>'File was uploaded successfuly!')));
        }

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Could not recognize what you dropped!');

        return new Response(json_encode(array('status'=>'Could not recognize anything!')));
    }

    /**
     * @param int    $folderId The folder id
     * @param string $type     The type
     *
     * @Route("create/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"}, name="KunstmaanMediaBundle_media_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function createAction($folderId, $type)
    {
        return $this->createAndRedirect($folderId, $type, "KunstmaanMediaBundle_folder_show");
    }

    /**
     * @param int    $folderId The folder id
     * @param string $type     The type
     *
     * @Route("create/modal/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"}, name="KunstmaanMediaBundle_media_modal_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function createModalAction($folderId, $type)
    {
        return $this->createAndRedirect($folderId, $type, "KunstmaanMediaBundle_chooser_show_folder");
    }

    /**
     * @param int    $folderId    The folder Id
     * @param string $type        The type
     * @param string $redirectUrl The url where we want to redirect to on success
     *
     * @return array
     */
    private function createAndRedirect($folderId, $type, $redirectUrl)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $handler = $mediaManager->getHandlerForType($type);
        $media = new Media();
        $helper = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $media = $helper->getMedia();
                $media->setFolder($folder);
                $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                $this->get('session')->getFlashBag()->add('success', 'Media \''.$media->getName().'\' has been created!');

                return new RedirectResponse($this->generateUrl($redirectUrl, array("folderId" => $folder->getId())));
            }
        }

        return array(
            'type' => $type,
            'form' => $form->createView(),
            'folder' => $folder
        );
    }

}
