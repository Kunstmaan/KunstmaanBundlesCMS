<?php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Entity\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        /* @var Folder $firstGallery */
        $firstFolder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder(1);
        //TODO get the first topfolder

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
                'folders' => $folders
        );
    }

}