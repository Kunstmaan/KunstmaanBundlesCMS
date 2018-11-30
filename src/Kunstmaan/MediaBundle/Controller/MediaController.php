<?php

namespace Kunstmaan\MediaBundle\Controller;

use Exception;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\BulkMoveMediaType;
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
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $folder = $media->getFolder();

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $handler = $mediaManager->getHandler($media);
        $helper = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper, $handler->getFormTypeOptions());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $media = $helper->getMedia();
                $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                return new RedirectResponse(
                    $this->generateUrl(
                        'KunstmaanMediaBundle_media_show',
                        ['mediaId' => $media->getId()]
                    )
                );
            }
        }
        $showTemplate = $mediaManager->getHandler($media)->getShowTemplate($media);

        return $this->render(
            $showTemplate,
            [
                'handler' => $handler,
                'foldermanager' => $this->get('kunstmaan_media.folder_manager'),
                'mediamanager' => $this->get('kunstmaan_media.media_manager'),
                'editform' => $form->createView(),
                'media' => $media,
                'helper' => $helper,
                'folder' => $folder,
            ]
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
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $medianame = $media->getName();
        $folder = $media->getFolder();

        $em->getRepository('KunstmaanMediaBundle:Media')->delete($media);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans(
                'kuma_admin.media.flash.deleted_success.%medianame%',
                [
                    '%medianame%' => $medianame,
                ]
            )
        );

        // If the redirect url is passed via the url we use it
        $redirectUrl = $request->query->get('redirectUrl');
        if (empty($redirectUrl) || (\strpos($redirectUrl, $request->getSchemeAndHttpHost()) !== 0 && \strpos($redirectUrl, '/') !== 0)) {
            $redirectUrl = $this->generateUrl(
                'KunstmaanMediaBundle_folder_show',
                ['folderId' => $folder->getId()]
            );
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkupload/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_bulk_upload")
     * @Template("@KunstmaanMedia/Media/bulkUpload.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function bulkUploadAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        return ['folder' => $folder];
    }

    /**
     * @param Request $request
     * @param int     $folderId
     *
     * @Route("bulkuploadsubmit/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_bulk_upload_submit")
     *
     * @return JsonResponse
     */
    public function bulkUploadSubmitAction(Request $request, $folderId)
    {
        // Settings
        if (\ini_get('upload_tmp_dir')) {
            $tempDir = \ini_get('upload_tmp_dir');
        } else {
            $tempDir = \sys_get_temp_dir();
        }
        $targetDir = \rtrim($tempDir, '/').DIRECTORY_SEPARATOR.'plupload';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 60 * 60; // Temp file age in seconds

        // Create target dir
        if (!\file_exists($targetDir)) {
            @\mkdir($targetDir);
        }

        // Get a file name
        if ($request->request->has('name')) {
            $fileName = $request->request->get('name');
        } elseif (0 !== $request->files->count()) {
            $fileName = $request->files->get('file')['name'];
        } else {
            $fileName = \uniqid('file_', false);
        }
        $filePath = $targetDir.DIRECTORY_SEPARATOR.$fileName;

        $chunk = 0;
        $chunks = 0;
        // Chunking might be enabled
        if ($request->request->has('chunk')) {
            $chunk = $request->request->getInt('chunk');
        }
        if ($request->request->has('chunks')) {
            $chunks = $request->request->getInt('chunks');
        }

        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!\is_dir($targetDir) || !$dir = \opendir($targetDir)) {
                return $this->returnJsonError('100', 'Failed to open temp directory.');
            }

            while (($file = \readdir($dir)) !== false) {
                $tmpFilePath = $targetDir.DIRECTORY_SEPARATOR.$file;

                // If temp file is current file proceed to the next
                if ($tmpFilePath === "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (\preg_match('/\.part$/', $file) && (\filemtime($tmpFilePath) < \time() - $maxFileAge)) {
                    $success = @\unlink($tmpFilePath);
                    if ($success !== true) {
                        return $this->returnJsonError('106', 'Could not remove temp file: '.$filePath);
                    }
                }
            }
            \closedir($dir);
        }

        // Open temp file
        if (!$out = @\fopen("{$filePath}.part", $chunks ? 'ab' : 'wb')) {
            return $this->returnJsonError('102', 'Failed to open output stream.');
        }

        if (0 !== $request->files->count()) {
            $_file = $request->files->get('file');
            if ($_file->getError() > 0 || !\is_uploaded_file($_file->getRealPath())) {
                return $this->returnJsonError('103', 'Failed to move uploaded file.');
            }

            // Read binary input stream and append it to temp file
            if (!$input = @\fopen($_file->getRealPath(), 'rb')) {
                return $this->returnJsonError('101', 'Failed to open input stream.');
            }
        } else {
            if (!$input = @\fopen('php://input', 'rb')) {
                return $this->returnJsonError('101', 'Failed to open input stream.');
            }
        }

        while ($buff = \fread($input, 4096)) {
            \fwrite($out, $buff);
        }

        @\fclose($out);
        @\fclose($input);

        // Check if file has been uploaded
        if (!$chunks || $chunk === $chunks - 1) {
            // Strip the temp .part suffix off
            \rename("{$filePath}.part", $filePath);
        }

        $em = $this->getDoctrine()->getManager();
        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $file = new File($filePath);

        try {
            /* @var Media $media */
            $media = $this->get('kunstmaan_media.media_manager')->getHandler($file)->createNew($file);
            $media->setFolder($folder);
            $em->getRepository(Media::class)->save($media);
        } catch (Exception $e) {
            return $this->returnJsonError('104', 'Failed performing save on media-manager');
        }

        $success = \unlink($filePath);
        if ($success !== true) {
            return $this->returnJsonError('105', 'Could not remove temp file: '.$filePath);
        }

        // Send headers making sure that the file is not cached (as it happens for example on iOS devices)
        $response = new JsonResponse(
            [
                'jsonrpc' => '2.0',
                'result' => '',
                'id' => 'id',
            ], JsonResponse::HTTP_OK, [
                'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => \gmdate('D, d M Y H:i:s').' GMT',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]
        );

        return $response;
    }

    private function returnJsonError($code, $message)
    {
        return new JsonResponse(
            [
                'jsonrpc' => '2.0',
                'error ' => [
                    'code' => $code,
                    'message' => $message,
                ],
                'id' => 'id',
            ]
        );
    }

    /**
     * @param Request $request
     * @param int     $folderId
     *
     * @Route("drop/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_drop_upload")
     * @Method({"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function dropAction(Request $request, $folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $drop = null;

        if ($request->files->has('files') && $request->files->get('files')['error'] === 0) {
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

            return new JsonResponse(['status' => $this->get('translator')->trans('kuma_admin.media.flash.drop_success')]);
        }

        $request->getSession()->getFlashBag()->add(
            FlashTypes::DANGER,
            $this->get('translator')->trans('kuma_admin.media.flash.drop_unrecognized')
        );

        return new JsonResponse(['status' => $this->get('translator')->trans('kuma_admin.media.flash.drop_unrecognized')]);
    }

    /**
     * @param Request $request
     * @param int     $folderId The folder id
     * @param string  $type     The type
     *
     * @Route("create/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"}, name="KunstmaanMediaBundle_media_create")
     * @Method({"GET", "POST"})
     * @Template("@KunstmaanMedia/Media/create.html.twig")
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
     * @return array|RedirectResponse
     */
    private function createAndRedirect(Request $request, $folderId, $type, $redirectUrl, $extraParams = [], $isInModal = false)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $handler = $mediaManager->getHandlerForType($type);
        $media = new Media();
        $helper = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper, $handler->getFormTypeOptions());

        if ($request->isMethod('POST')) {
            $params = ['folderId' => $folder->getId()];
            $params = \array_merge($params, $extraParams);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $media = $helper->getMedia();
                $media->setFolder($folder);
                $em->getRepository('KunstmaanMediaBundle:Media')->save($media);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->get('translator')->trans(
                        'media.flash.created',
                        [
                            '%medianame%' => $media->getName(),
                        ]
                    )
                );

                return new RedirectResponse($this->generateUrl($redirectUrl, $params));
            }

            if ($isInModal) {
                $this->addFlash(
                    FlashTypes::ERROR,
                    $this->get('translator')->trans(
                        'media.flash.not_created',
                        [
                            '%mediaerrors%' => $form->getErrors(true, true),
                        ]
                    )
                );

                return new RedirectResponse($this->generateUrl($redirectUrl, $params));
            }
        }

        return [
            'type' => $type,
            'form' => $form->createView(),
            'folder' => $folder,
        ];
    }

    /**
     * @param Request $request
     * @param int     $folderId The folder id
     * @param string  $type     The type
     *
     * @Route("create/modal/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"}, name="KunstmaanMediaBundle_media_modal_create")
     * @Method({"POST"})
     *
     * @return array|RedirectResponse
     */
    public function createModalAction(Request $request, $folderId, $type)
    {
        $cKEditorFuncNum = $request->get('CKEditorFuncNum');
        $linkChooser = $request->get('linkChooser');

        $extraParams = [];
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
            $extraParams,
            true
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
        @trigger_error(sprintf('The "%s" controller action is deprecated in KunstmaanMediaBundle 5.1 and will be removed in KunstmaanMediaBundle 6.0.', __METHOD__), E_USER_DEPRECATED);

        $mediaId = $request->request->get('mediaId');
        $folderId = $request->request->get('folderId');

        if (empty($mediaId) || empty($folderId)) {
            return new JsonResponse(['error' => ['title' => 'Missing media id or folder id']], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $mediaRepo = $em->getRepository('KunstmaanMediaBundle:Media');

        $media = $mediaRepo->getMedia($mediaId);
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);

        $media->setFolder($folder);
        $mediaRepo->save($media);

        return new JsonResponse();
    }

    /**
     * @Route("/bulk-move", name="KunstmaanMediaBundle_media_bulk_move")
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function bulkMoveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mediaRepo = $em->getRepository('KunstmaanMediaBundle:Media');
        $form = $this->createForm(BulkMoveMediaType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Folder $folder */
            $folder = $form->getData()['folder'];
            $mediaIds = explode(',', $form->getData()['media']);

            $mediaRepo->createQueryBuilder('m')
                ->update()
                ->set('m.folder', $folder->getId())
                ->where('m.id in (:mediaIds)')
                ->setParameter('mediaIds', $mediaIds)
                ->getQuery()
                ->execute();

            $this->addFlash(FlashTypes::SUCCESS, $this->get('translator')->trans('media.folder.bulk_move.success.text'));

            return new JsonResponse(
                [
                    'Success' => 'The media is moved',
                ]
            );
        }

        return $this->render(
            '@KunstmaanMedia/Folder/bulk-move-modal_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
