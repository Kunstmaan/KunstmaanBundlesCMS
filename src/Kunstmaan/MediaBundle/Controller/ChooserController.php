<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * chooser controller.
 *
 */
class ChooserController extends Controller
{

    /**
     * @Route("/chooser", name="KunstmaanMediaBundle_chooser")
     *
     * @return RedirectResponse
     */
    public function chooserIndexAction()
    {
        $type = $this->getRequest()->get('type');
        $cKEditorFuncNum = $this->getRequest()->get("CKEditorFuncNum");

        $em = $this->getDoctrine()->getManager();

        /* @var Folder $firstFolder */
        $firstFolder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFirstTopFolder();

        return $this->redirect($this->generateUrl("KunstmaanMediaBundle_chooser_show_folder", array("folderId" => $firstFolder->getId(), "type" => $type, "CKEditorFuncNum" => $cKEditorFuncNum)));
    }

    /**
     * @param int $folderId The filder id
     *
     * @Route("/chooser/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function chooserShowFolderAction($folderId)
    {
        $type = $this->getRequest()->get('type');
        $cKEditorFuncNum = $this->getRequest()->get("CKEditorFuncNum");

        $em = $this->getDoctrine()->getManager();
        /* @var MediaManager $mediaHandler */
        $mediaHandler = $this->get('kunstmaan_media.media_manager');

        /* @var Folder $folder */
        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        /* @var array $mediaHandler */
        $folders = $em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();

        $handler = null;
        if ($type) {
            $handler = $mediaHandler->getHandlerForType($type);
        }

        return array(
                "cKEditorFuncNum" => $cKEditorFuncNum,
                'mediamanager' => $mediaHandler,
                'handler' => $handler,
                'type'    => $type,
                'folder'  => $folder,
                'folders' => $folders,
                'fileform' => $this->createTypeFormView($mediaHandler, "file"),
                'videoform' => $this->createTypeFormView($mediaHandler, "video"),
                'slideform' => $this->createTypeFormView($mediaHandler, "slide")
        );
    }

    /**
     * @param MediaManager $mediaManager
     * @param String       $type
     *
     * @return \Symfony\Component\Form\FormView
     */
    private function createTypeFormView(MediaManager $mediaManager, $type)
    {
        $handler = $mediaManager->getHandlerForType($type);
        $media = new Media();
        $helper = $handler->getFormHelper($media);

        return $this->createForm($handler->getFormType(), $helper)->createView();
    }

}
