<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\MediaBundle\Helper\Event\MediaEvent;
use Kunstmaan\MediaBundle\Helper\Event\Events;
use Kunstmaan\MediaBundle\Form\BulkUploadType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
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

class MediaController extends Controller
{

    /**
     * @Route("/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_media_show")
     */
    public function showAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $media     = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);
        $gallery   = $media->getGallery();

        $mediaManager = $this->get('kunstmaan_media.manager');
        $mediaContext = $mediaManager->getContext($media->getContext());
        $metadataClass = $mediaContext->getMetadataClass();

        return $this->render('KunstmaanMediaBundle:' . $media->getClassType() . ':show.html.twig', array(
                                                                                                        'media'     => $media,
                                                                                                        'gallery'   => $gallery,
                                                                                                        'hasmetadata' => (isset($metadataClass) ? TRUE : FALSE)
                                                                                                   ));
    }

    /**
     * @Route("/delete/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_media_delete")
     */
    public function deleteAction($media_id)
    {
        $em      = $this->getDoctrine()->getEntityManager();
        $media   = $em->getRepository('KunstmaanMediaBundle:Media')->getMedia($media_id, $em);
        $gallery = $media->getGallery();

        $em->getRepository('KunstmaanMediaBundle:Media')->delete($media, $em);

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id'  => $gallery->getId(),
                                                                                                'slug' => $gallery->getSlug()
                                                                                           )));
    }

    /**
     * @Route("bulkupload/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_bulkupload")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:File:bulkupload.html.twig")
     */
    public function bulkUploadAction($gallery_id)
    {
        $em      = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);
        $strategy = $gallery->getStrategy();

        $newMediaInstance = $strategy->getNewBulkUploadMediaInstance();

        if(is_null($newMediaInstance)) {
            throw new \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException("This type of gallery doesn't support multiple file upload.");
        }

        $request = $this->getRequest();
        $helper  = new BulkUploadHelper();

        $form = $this->createForm(new BulkUploadType($strategy->getBulkUploadAccept()), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {

                foreach ($helper->getFiles() as $file) {

                    $media = clone $newMediaInstance;
                    $media->setName($file->getClientOriginalName());
                    $media->setContent($file);
                    $media->setGallery($gallery);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($media, $em);

                }

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array(
                                                                                                        'id'  => $gallery->getId(),
                                                                                                        'slug' => $gallery->getSlug()
                                                                                                   )));
            }
        }

        $formView = $form->createView();
        $formView->getChild('files')->set('full_name', 'kunstmaan_mediabundle_bulkupload[files][]');

        return array(
            'form'      => $formView,
            'gallery'   => $gallery
        );

    }

    /**
     * @Route("filecreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_filecreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:File:create.html.twig")
     */
    public function filecreateAction($gallery_id)
    {
        $em      = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);

        $request = $this->getRequest();
        $helper  = new MediaHelper();

        $formbuilder = $this->createFormBuilder();
        $formbuilder->add('media', new MediaType());
        $bindingarray = array('media' => $helper);

        $metadataClass = $this->getMetadataClass(File::CONTEXT);

        if (isset($metadataClass)) {
            $metadata = new $metadataClass();
            $formbuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingarray['metadata'] = $metadata;
        }

        $formbuilder->setData($bindingarray);
        $form = $formbuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($helper->getMedia() != NULL) {
                    $file = new File();
                    $file->setName($helper->getMedia()->getClientOriginalName());
                    $file->setContent($helper->getMedia());
                    $file->setGallery($gallery);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($file, $em);

                    if (isset($metadata)) {
                        $metadata->setMedia($file);
                        $em->persist($metadata);
                        $em->flush();
                    }

                    $dispatcher = $this->get('event_dispatcher');
                    if ($dispatcher->hasListeners(Events::postCreate)) {
                        $event = new MediaEvent($file, isset($metadata)? $metadata : null);
                        $dispatcher->dispatch(Events::postCreate, $event);
                    }

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id'  => $gallery->getId(),
                                                                                                            'slug' => $gallery->getSlug()
                                                                                                       )));
                }
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $gallery
        );
    }

    /**
     * @Route("imagecreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_imagecreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Image:create.html.twig")
     */
    public function imagecreateAction($gallery_id)
    {
        $em      = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);

        $request       = $this->getRequest();
        $picturehelper = new MediaHelper();

        $formbuilder = $this->createFormBuilder();
        $formbuilder->add('media', new MediaType());
        $bindingarray = array('media' => $picturehelper);

        $metadataClass = $this->getMetadataClass(Image::CONTEXT);

        if (isset($metadataClass)) {
            $metadata = new $metadataClass();
            $formbuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingarray['metadata'] = $metadata;
        }

        $formbuilder->setData($bindingarray);
        $form = $formbuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($picturehelper->getMedia() != NULL) {
                    $picture = new Image();
                    $picture->setName($picturehelper->getMedia()->getClientOriginalName());
                    $picture->setContent($picturehelper->getMedia());
                    $picture->setGallery($gallery);

                    $em->getRepository('KunstmaanMediaBundle:Media')->save($picture, $em);

                    if (isset($metadata)) {
                        $metadata->setMedia($picture);
                        $em->persist($metadata);
                        $em->flush();
                    }

                    $dispatcher = $this->get('event_dispatcher');
                    if ($dispatcher->hasListeners(Events::postCreate)) {
                        $event = new MediaEvent($picture, isset($metadata)? $metadata : null);
                        $dispatcher->dispatch(Events::postCreate, $event);
                    }

                    return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id'  => $gallery->getId(),
                                                                                                                                              'slug' => $gallery->getSlug()
                                                                                                                                         )));
                }
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $gallery
        );
    }

    /**
     * @Route("videocreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_videocreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Video:create.html.twig")
     */
    public function videocreateAction($gallery_id)
    {
        $em      = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);

        $request = $this->getRequest();
        $video   = new Video();

        $formbuilder = $this->createFormBuilder();
        $formbuilder->add('media', new VideoType());
        $bindingarray = array('media' => $video);

        $metadataClass = $this->getMetadataClass(Video::CONTEXT);

        if (isset($metadataClass)) {
            $metadata = new $metadataClass();
            $formbuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingarray['metadata'] = $metadata;
        }

        $formbuilder->setData($bindingarray);
        $form = $formbuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $video->setGallery($gallery);

                $em->getRepository('KunstmaanMediaBundle:Media')->save($video, $em);

                if (isset($metadata)) {
                    $metadata->setMedia($video);
                    $em->persist($metadata);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::postCreate)) {
                    $event = new MediaEvent($video, isset($metadata)? $metadata : null);
                    $dispatcher->dispatch(Events::postCreate, $event);
                }

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id'  => $gallery->getId(),
                                                                                                                                          'slug' => $gallery->getSlug()
                                                                                                                                     )));
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $gallery
        );
    }

    /**
     * @Route("slidecreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_slidecreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Slide:create.html.twig")
     */
    public function slidecreateAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id, $em);

        $request = $this->getRequest();
        $slide   = new Slide();

        $formbuilder = $this->createFormBuilder();
        $formbuilder->add('media', new SlideType());
        $bindingarray = array('media' => $slide);

        $metadataClass = $this->getMetadataClass(Slide::CONTEXT);

        if (isset($metadataClass)) {
            $metadata = new $metadataClass();
            $formbuilder->add('metadata', $metadata->getDefaultAdminType());
            $bindingarray['metadata'] = $metadata;
        }

        $formbuilder->setData($bindingarray);
        $form = $formbuilder->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $slide->setGallery($gallery);

                $em->getRepository('KunstmaanMediaBundle:Media')->save($slide, $em);

                if (isset($metadata)) {
                    $metadata->setMedia($picture);
                    $em->persist($slide);
                    $em->flush();
                }

                $dispatcher = $this->get('event_dispatcher');
                if ($dispatcher->hasListeners(Events::postCreate)) {
                    $event = new MediaEvent($slide, isset($metadata)? $metadata : null);
                    $dispatcher->dispatch(Events::postCreate, $event);
                }

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id'  => $gallery->getId(),
                                                                                                                                          'slug' => $gallery->getSlug()
                                                                                                                                     )));
            }
        }

        return array(
            'form'      => $form->createView(),
            'gallery'   => $gallery
        );
    }

    private function getMetadataClass($context = File::CONTEXT)
    {
        $mediaManager = $this->get('kunstmaan_media.manager');
        $imageContext = $mediaManager->getContext($context);
        return $imageContext->getMetadataClass();
    }

}
