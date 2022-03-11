<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\BulkMoveMediaType;
use Kunstmaan\MediaBundle\Helper\FolderManager;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MediaController extends AbstractController
{
    /** @var MediaManager */
    private $mediaManager;
    /** @var FolderManager */
    private $folderManager;
    /** @var TranslatorInterface */
    private $translator;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(MediaManager $mediaManager, FolderManager $folderManager, TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->mediaManager = $mediaManager;
        $this->folderManager = $folderManager;
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * @param int $mediaId
     *
     * @Route("/{mediaId}", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_show")
     *
     * @return Response
     */
    public function showAction(Request $request, $mediaId)
    {
        /* @var Media $media */
        $media = $this->em->getRepository(Media::class)->getMedia($mediaId);
        $folder = $media->getFolder();

        $handler = $this->mediaManager->getHandler($media);
        $helper = $handler->getFormHelper($media);

        $form = $this->createForm($handler->getFormType(), $helper, $handler->getFormTypeOptions());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $media = $helper->getMedia();
                $this->em->getRepository(Media::class)->save($media);

                return $this->redirectToRoute('KunstmaanMediaBundle_media_show', ['mediaId' => $media->getId()]);
            }
        }
        $showTemplate = $this->mediaManager->getHandler($media)->getShowTemplate($media);

        return $this->render($showTemplate, [
            'handler' => $handler,
            'foldermanager' => $this->folderManager,
            'mediamanager' => $this->mediaManager,
            'editform' => $form->createView(),
            'media' => $media,
            'helper' => $helper,
            'folder' => $folder,
        ]);
    }

    /**
     * @param int $mediaId
     *
     * @Route("/delete/{mediaId}", requirements={"mediaId" = "\d+"}, name="KunstmaanMediaBundle_media_delete", methods={"POST"})
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $mediaId)
    {
        if (!$this->isCsrfTokenValid('media-delete', $request->request->get('token'))) {
            return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_show', ['mediaId' => $mediaId]));
        }

        /* @var Media $media */
        $media = $this->em->getRepository(Media::class)->getMedia($mediaId);
        $medianame = $media->getName();
        $folder = $media->getFolder();

        $this->em->getRepository(Media::class)->delete($media);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->translator->trans(
                'kuma_admin.media.flash.deleted_success.%medianame%',
                [
                    '%medianame%' => $medianame,
                ]
            )
        );

        // If the redirect url is passed via the url we use it
        $redirectUrl = $request->query->get('redirectUrl');
        if (empty($redirectUrl) || (\strpos($redirectUrl, $request->getSchemeAndHttpHost()) !== 0 && strncmp($redirectUrl, '/', 1) !== 0)) {
            $redirectUrl = $this->generateUrl('KunstmaanMediaBundle_folder_show', ['folderId' => $folder->getId()]);
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkupload/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_bulk_upload")
     */
    public function bulkUploadAction($folderId): Response
    {
        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);

        return $this->render('@KunstmaanMedia/Media/bulkUpload.html.twig', ['folder' => $folder]);
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkuploadsubmit/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_bulk_upload_submit", methods={"POST"})
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
        $targetDir = \rtrim($tempDir, '/') . DIRECTORY_SEPARATOR . 'plupload';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 60 * 60; // Temp file age in seconds

        // Create target dir
        if (!\file_exists($targetDir)) {
            @\mkdir($targetDir);
        }

        $submittedToken = $request->headers->get('x-upload-token');
        if (!$this->isCsrfTokenValid('bulk-upload-media', $submittedToken)) {
            return $this->returnJsonError('105', 'Could not verify token');
        }

        // Get a file name
        if ($request->request->has('name')) {
            $fileName = $request->request->get('name');
        } elseif (0 !== $request->files->count()) {
            $fileName = $request->files->get('file')['name'];
        } else {
            $fileName = \uniqid('file_', false);
        }
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

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
                $tmpFilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpFilePath === "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (\preg_match('/\.part$/', $file) && (\filemtime($tmpFilePath) < \time() - $maxFileAge)) {
                    $success = @\unlink($tmpFilePath);
                    if ($success !== true) {
                        return $this->returnJsonError('106', 'Could not remove temp file: ' . $filePath);
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

        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);
        $file = new File($filePath);

        try {
            $media = $this->mediaManager->getHandler($file)->createNew($file);
            $media->setFolder($folder);
            $this->em->getRepository(Media::class)->save($media);
        } catch (\Exception $e) {
            return $this->returnJsonError('104', 'Failed performing save on media-manager');
        }

        $success = \unlink($filePath);
        if ($success !== true) {
            return $this->returnJsonError('105', 'Could not remove temp file: ' . $filePath);
        }

        // Send headers making sure that the file is not cached (as it happens for example on iOS devices)
        $response = new JsonResponse(
            [
                'jsonrpc' => '2.0',
                'result' => '',
                'id' => 'id',
            ], JsonResponse::HTTP_OK, [
                'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => \gmdate('D, d M Y H:i:s') . ' GMT',
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
     * @param int $folderId
     *
     * @Route("drop/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_media_drop_upload", methods={"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function dropAction(Request $request, $folderId)
    {
        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);

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
        $media = $this->mediaManager->createNew($drop);
        if ($media) {
            $media->setFolder($folder);
            $this->em->getRepository(Media::class)->save($media);

            return new JsonResponse(['status' => $this->translator->trans('kuma_admin.media.flash.drop_success')]);
        }

        $request->getSession()->getFlashBag()->add(
            FlashTypes::DANGER,
            $this->translator->trans('kuma_admin.media.flash.drop_unrecognized')
        );

        return new JsonResponse(['status' => $this->translator->trans('kuma_admin.media.flash.drop_unrecognized')]);
    }

    /**
     * @param int    $folderId The folder id
     * @param string $type     The type
     *
     * @Route("create/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"}, name="KunstmaanMediaBundle_media_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, $folderId, $type): Response
    {
        $responseOrData = $this->createAndRedirect($request, $folderId, $type, 'KunstmaanMediaBundle_folder_show');
        if ($responseOrData instanceof Response) {
            return $responseOrData;
        }

        return $this->render('@KunstmaanMedia/Media/create.html.twig', $responseOrData);
    }

    /**
     * @param int    $folderId    The folder Id
     * @param string $type        The type
     * @param string $redirectUrl The url where we want to redirect to on success
     * @param array  $extraParams The extra parameters that will be passed wen redirecting
     *
     * @return array|RedirectResponse
     */
    private function createAndRedirect(Request $request, $folderId, $type, $redirectUrl, $extraParams = [], $isInModal = false)
    {
        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);

        $handler = $this->mediaManager->getHandlerForType($type);
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
                $this->em->getRepository(Media::class)->save($media);

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->translator->trans(
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
                    FlashTypes::DANGER,
                    $this->translator->trans(
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
     * @param int    $folderId The folder id
     * @param string $type     The type
     *
     * @Route("create/modal/{folderId}/{type}", requirements={"folderId" = "\d+", "type" = ".+"}, name="KunstmaanMediaBundle_media_modal_create", methods={"POST"})
     *
     * @return array|RedirectResponse
     */
    public function createModalAction(Request $request, $folderId, $type)
    {
        $cKEditorFuncNum = $request->query->get('CKEditorFuncNum');
        $linkChooser = $request->query->get('linkChooser');

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
     * @Route("/bulk-move", name="KunstmaanMediaBundle_media_bulk_move")
     *
     * @return JsonResponse|Response
     */
    public function bulkMoveAction(Request $request)
    {
        $mediaRepo = $this->em->getRepository(Media::class);
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

            $this->addFlash(FlashTypes::SUCCESS, $this->translator->trans('media.folder.bulk_move.success.text'));

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
