<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

use Symfony\Component\BrowserKit\Response;

use Kunstmaan\AdminBundle\Helper\ClassLookup;
use Kunstmaan\MediaBundle\Event\MediaEvent;
use Kunstmaan\MediaBundle\Event\Events;
use Kunstmaan\MediaBundle\Form\BulkUploadType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Entity\Video;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\File;
use Kunstmaan\MediaBundle\Form\MediaType;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
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

        /* @var Media $media */
        $media = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $folder = $media->getGallery();

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.manager');
        $mediaContext = $mediaManager->getContext($media->getContext());
        $metadataClass = $mediaContext->getMetadataClass();

        return $this->render('KunstmaanMediaBundle:' . $media->getClassType() . ':show.html.twig', array(
            'media'     => $media,
            'gallery'   => $folder,
            'hasmetadata' => (isset($metadataClass) ? true : false)
        ));
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
        $em      = $this->getDoctrine()->getManager();

        /* @var Media $media */
        $media   = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($mediaId);
        $folder = $media->getGallery();

        $em->getRepository('KunstmaanMediaBundle:Media')->delete($media);

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId(),
            'slug' => $folder->getSlug()
        )));
    }

    /**
     * @param int $folderId
     *
     * @Route("bulkupload/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_bulk_upload")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:File:bulkupload.html.twig")
     *
     * @return array|RedirectResponse
     *
     * @throws \InvalidArgumentException when the gallery does not support bulk upload
     */
    public function bulkUploadAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $strategy = $folder->getStrategy();

        $newMediaInstance = $strategy->getNewBulkUploadMediaInstance();

        if (is_null($newMediaInstance)) {
            throw new \InvalidArgumentException("This type of gallery doesn't support multiple file upload.");
        }

        $request = $this->getRequest();
        $helper  = new BulkUploadHelper();

        $form = $this->createForm(new BulkUploadType($strategy->getBulkUploadAccept()), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                foreach ($helper->getFiles() as $file) {
                    /* @var Media $media */
                    $media = clone $newMediaInstance;
                    $media->setName($file->getClientOriginalName());
                    $media->setContent($file);
                    $media->setGallery($folder);
                    $em->getRepository('KunstmaanMediaBundle:Media')->save($media);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array(
                    'folderId'  => $folder->getId(),
                    'slug' => $folder->getSlug()
                )));
            }
        }

        $formView = $form->createView();
        $filesfield = $formView->children['files'];
        $filesfield->set('full_name', 'kunstmaan_mediabundle_bulkupload[files][]');

        return array(
            'form'      => $formView,
            'gallery'   => $folder
        );

    }

    /**
     * @param int $folderId
     *
     * @Route("filecreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_file_create")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:File:create.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function fileCreateAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $helper  = new MediaHelper();

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('media', new MediaType());
        $bindingArray = array('media' => $helper);

        $metadataClass = $this->getMetadataClass(File::CONTEXT);

        $metadata = null;
        if (isset($metadataClass)) {
            /* @var AbstractMediaMetadata $metadata */
            $metadata = new $metadataClass();
            $formBuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingArray['metadata'] = $metadata;
        }

        $formBuilder->setData($bindingArray);
        $form = $formBuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($helper->getMedia() != null) {
                    $file = new File();
                    $file->setName($helper->getMedia()->getClientOriginalName());
                    $file->setContent($helper->getMedia());
                    $file->setGallery($folder);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($file);

                    if (isset($metadata)) {
                        $metadata->setMedia($file);
                        $em->persist($metadata);
                        $em->flush();
                    }

                    $dispatcher = $this->get('event_dispatcher');
                    if ($dispatcher->hasListeners(Events::POST_CREATE)) {
                        $event = new MediaEvent($file, isset($metadata) ? $metadata : null);
                        $dispatcher->dispatch(Events::POST_CREATE, $event);
                    }

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId(),
                        'slug' => $folder->getSlug()
                    )));
                }
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $folder
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("imagecreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_image_create")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Image:create.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function imageCreateAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $pictureHelper = new MediaHelper();

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('media', new MediaType());
        $bindingArray = array('media' => $pictureHelper);

        $metadataClass = $this->getMetadataClass(Image::CONTEXT);

        if (isset($metadataClass)) {
            /* @var AbstractMediaMetadata $metadata */
            $metadata = new $metadataClass();
            $formBuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingArray['metadata'] = $metadata;
        }

        $formBuilder->setData($bindingArray);
        $form = $formBuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($pictureHelper->getMedia() != null) {
                    $picture = new Image();
                    $picture->setName($pictureHelper->getMedia()->getClientOriginalName());
                    $picture->setContent($pictureHelper->getMedia());
                    $picture->setGallery($folder);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($picture);

                    if (isset($metadata)) {
                        $metadata->setMedia($picture);
                        $em->persist($metadata);
                        $em->flush();
                    }

                    $dispatcher = $this->get('event_dispatcher');
                    if ($dispatcher->hasListeners(Events::POST_CREATE)) {
                        $event = new MediaEvent($picture, isset($metadata) ? $metadata : null);
                        $dispatcher->dispatch(Events::POST_CREATE, $event);
                    }

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId(),
                        'slug' => $folder->getSlug()
                    )));
                }
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $folder
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("videocreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_video_create")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Video:create.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function videoCreateAction($folderId)
    {
        $em      = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $video   = new Video();

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('media', new VideoType());
        $bindingArray = array('media' => $video);

        $metadataClass = $this->getMetadataClass(Video::CONTEXT);

        if (isset($metadataClass)) {
            /* @var AbstractMediaMetadata $metadata */
            $metadata = new $metadataClass();
            $formBuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingArray['metadata'] = $metadata;
        }

        $formBuilder->setData($bindingArray);
        $form = $formBuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $video->setGallery($folder);

                $em->getRepository('KunstmaanMediaBundle:Media')->save($video);

                if (isset($metadata)) {
                    $metadata->setMedia($video);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::POST_CREATE)) {
                    $event = new MediaEvent($video, isset($metadata) ? $metadata : null);
                    $dispatcher->dispatch(Events::POST_CREATE, $event);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId(),
                    'slug' => $folder->getSlug()
                )));
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $folder
        );
    }

    /**
     * @param int $folderId
     *
     * @Route("slidecreate/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_folder_slide_create")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Slide:create.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function slideCreateAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $slide   = new Slide();

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('media', new SlideType());
        $bindingArray = array('media' => $slide);

        $metadataClass = $this->getMetadataClass(Slide::CONTEXT);

        if (isset($metadataClass)) {
            /* @var AbstractMediaMetadata $metadata */
            $metadata = new $metadataClass();
            $formBuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingArray['metadata'] = $metadata;
        }

        $formBuilder->setData($bindingArray);
        $form = $formBuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $slide->setGallery($folder);

                $em->getRepository('KunstmaanMediaBundle:Media')->save($slide);

                if (isset($metadata)) {
                    $metadata->setMedia($slide);
                    $em->persist($slide);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::POST_CREATE)) {
                    $event = new MediaEvent($slide, isset($metadata) ? $metadata : null);
                    $dispatcher->dispatch(Events::POST_CREATE, $event);
                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('folderId'  => $folder->getId(),
                    'slug' => $folder->getSlug()
                )));
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $folder
        );
    }

    /**
     * @param string $context
     *
     * @return AbstractMediaMetadata
     */
    private function getMetadataClass($context = File::CONTEXT)
    {
        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);

        return $imageContext->getMetadataClass();
    }

}
