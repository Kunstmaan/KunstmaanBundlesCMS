<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MediaController
 */
class MediaController extends Controller
{
    /**
     * @param Request $request
     * @param int     $mediaId
     *
     * @Route("/{mediaId}", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_show")
     *
     * @return Response
     */
    public function showAction(Request $request, $mediaId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Media $media */
        $media  = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $folder = $media->getFolder();

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $handler      = $mediaManager->getHandler($media);
        $helper       = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $media = $helper->getMedia();
                $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                return new RedirectResponse(
                    $this->generateUrl(
                        'KunstmaanMediaBundle_media_show',
                        array('mediaId' => $media->getId())
                    )
                );
            }
        }
        $showTemplate = $mediaManager->getHandler($media)->getShowTemplate($media);

        return $this->render(
            $showTemplate,
            array(
                'handler'       => $handler,
                'foldermanager' => $this->get('kunstmaan_media.folder_manager'),
                'mediamanager'  => $this->get('kunstmaan_media.media_manager'),
                'editform'      => $form->createView(),
                'media'         => $media,
                'helper'        => $helper,
                'folder'        => $folder
            )
        );
    }

    /**
     * @param Request $request
     * @param int     $mediaId
     *
     * @Route("/delete/{mediaId}", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_delete")
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $mediaId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Media $media */
        $media     = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $medianame = $media->getName();
        $folder    = $media->getFolder();

        $em->getRepository('KunstmaanMediaBundle:Media')->delete($media);

        $this->get('session')->getFlashBag()->add('success', 'Entry \'' . $medianame . '\' has been deleted!');

        // If the redirect url is passed via the url we use it
        $redirectUrl = $request->query->get('redirectUrl');
        if (empty($redirectUrl) ||
            (strpos($redirectUrl, $request->getSchemeAndHttpHost()) !== 0 && strpos($redirectUrl, '/') !== 0)) {
            $redirectUrl = $this->generateUrl(
                'KunstmaanMediaBundle_folder_show',
                array('folderId' => $folder->getId())
            );
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkupload/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_bulk_upload")
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function bulkUploadAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        return array('folder' => $folder);
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkuploadsubmit/{folderId}", requirements={"folderId" = "\d+"},
     *                                       name="KunstmaanMediaBundle_media_bulk_upload_submit")
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function bulkUploadSubmitAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Settings
        $tempDir          = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
        $targetDir        = rtrim($tempDir, '/') . DIRECTORY_SEPARATOR . "plupload";
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge       = 5 * 60 * 60; // Temp file age in seconds

        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk  = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $response = new Response(
                    '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}'
                );
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        // Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            $response = new Response(
                '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}'
            );
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                $response = new Response(
                    '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}'
                );
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                $response = new Response(
                    '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}'
                );
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                $response = new Response(
                    '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}'
                );
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);
        }

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $file   = new File($filePath);

        /* @var Media $media */
        $media = $this->get('kunstmaan_media.media_manager')->getHandler($file)->createNew($file);
        $media->setFolder($folder);
        $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

        // Return Success JSON-RPC response
        return new JsonResponse(
            array(
                'jsonrpc' => '2.0',
                'result'  => '',
                'id'      => 'id'
            )
        );
    }

    /**
     * @param Request $request
     * @param int     $folderId
     *
     * @Route("drop/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_drop_upload")
     * @Method({"GET", "POST"})
     *
     * @return array|RedirectResponse
     */
    public function dropAction(Request $request, $folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $drop = null;

        if (array_key_exists('files', $_FILES) && $_FILES['files']['error'] == 0) {
            $drop = $request->files->get('files');
        } else {
            if ($request->files->get('file')) {
                $drop = $request->files->get('file');
            } else {
                $drop = $request->get('text');
            }
        }
        $media = $this->get('kunstmaan_media.media_manager')->createNew($drop);
        if ($media) {
            $media->setFolder($folder);
            $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

            return new Response(json_encode(array('status' => 'File was uploaded successfuly!')));
        }

        $request->getSession()->getFlashBag()->add('danger', 'Could not recognize what you dropped!');

        return new Response(json_encode(array('status' => 'Could not recognize anything!')));
    }

    /**
     * @param Request $request
     * @param int     $folderId The folder id
     * @param string  $type     The type
     *
     * @Route("create/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"},
     *                                    name="KunstmaanMediaBundle_media_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function createAction(Request $request, $folderId, $type)
    {
        return $this->createAndRedirect($request, $folderId, $type, 'KunstmaanMediaBundle_folder_show');
    }

    /**
     * @param Request $request
     * @param int     $folderId    The folder Id
     * @param string  $type        The type
     * @param string  $redirectUrl The url where we want to redirect to on success
     * @param array   $extraParams The extra parameters that will be passed wen redirecting
     *
     * @return array
     */
    protected function createAndRedirect(Request $request, $folderId, $type, $redirectUrl, $extraParams = array())
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $handler      = $mediaManager->getHandlerForType($type);
        $media        = new Media();
        $helper       = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $media = $helper->getMedia();
                $media->setFolder($folder);
                $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Media \'' . $media->getName() . '\' has been created!'
                );

                $params = array('folderId' => $folder->getId());
                $params = array_merge($params, $extraParams);

                return new RedirectResponse($this->generateUrl($redirectUrl, $params));
            }
        }

        return array(
            'type'   => $type,
            'form'   => $form->createView(),
            'folder' => $folder
        );
    }

    /**
     * @param Request $request
     * @param int     $folderId The folder id
     * @param string  $type     The type
     *
     * @Route("create/modal/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"},
     *                                          name="KunstmaanMediaBundle_media_modal_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function createModalAction(Request $request, $folderId, $type)
    {
        $cKEditorFuncNum = $request->get('CKEditorFuncNum');
        $linkChooser     = $request->get('linkChooser');

        $extraParams = array();
        if (!empty($cKEditorFuncNum)) {
            $extraParams['CKEditorFuncNum'] = $cKEditorFuncNum;
        }
        if (!empty($linkChooser)) {
            $extraParams['linkChooser'] = $linkChooser;
        }

        return $this->createAndRedirect(
            $request,
            $folderId,
            $type,
            'KunstmaanMediaBundle_chooser_show_folder',
            $extraParams
        );
    }

    /**
     * @param Request $request
     *
     * @Route("move/", name="KunstmaanMediaBundle_media_move")
     * @Method({"POST"})
     *
     * @return string
     */
    public function moveMedia(Request $request)
    {
        $mediaId  = $request->request->get('mediaId');
        $folderId = $request->request->get('folderId');

        if (empty($mediaId) || empty($folderId)) {
            return new JsonResponse(array('error' => array('title' => 'Missing media id or folder id')), 400);
        }

        $em        = $this->getDoctrine()->getManager();
        $mediaRepo = $em->getRepository('KunstmaanMediaBundle:Media');

        $media  = $mediaRepo->getMedia($mediaId);
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $media->setFolder($folder);
        $mediaRepo->save($media);

        return new JsonResponse();
    }
}
