<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * ChooserController.
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
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();

        $type = $this->getRequest()->get('type');
        $cKEditorFuncNum = $this->getRequest()->get('CKEditorFuncNum');
        $linkChooser = $this->getRequest()->get('linkChooser');

        // Go to the last visited folder
        if ($session->get('last-media-folder')) {
            $folderId = $session->get('last-media-folder');
        } else {
            // Redirect to the first top folder
            /* @var Folder $firstFolder */
            $firstFolder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFirstTopFolder();
            $folderId = $firstFolder->getId();
        }

        $params = array(
            'folderId'        => $folderId,
            'type'            => $type,
            'CKEditorFuncNum' => $cKEditorFuncNum,
            'linkChooser'     => $linkChooser
        );

        return $this->redirect($this->generateUrl('KunstmaanMediaBundle_chooser_show_folder', $params));
    }

    /**
     * @param int $folderId The folder id
     *
     * @Route("/chooser/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function chooserShowFolderAction($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session = $request->getSession();

        $type = $this->getRequest()->get('type');
        $cKEditorFuncNum = $this->getRequest()->get('CKEditorFuncNum');
        $linkChooser = $this->getRequest()->get('linkChooser');

        // Remember the last visited folder in the session
        $session->set('last-media-folder', $folderId);

        // Check when user switches between thumb -and list view
        $viewMode = $request->query->get('viewMode');
        if ($viewMode && $viewMode == 'list-view') {
            $session->set('media-list-view', true);
        } elseif ($viewMode && $viewMode == 'thumb-view') {
            $session->remove('media-list-view');
        }

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

        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');

        $adminListConfigurator = new MediaAdminListConfigurator($em, null, $mediaManager, $folder, $request);
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList($adminListConfigurator);
        $adminList->bindRequest($request);

        $linkChooserLink = null;
        if (!empty($linkChooser)) {
            $params = array();
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
                $routeName = 'KunstmaanNodeBundle_ckselecturl';
            } else {
                $routeName = 'KunstmaanNodeBundle_selecturl';
            }
            $linkChooserLink = $this->generateUrl($routeName, $params);
        }

        return array(
            'cKEditorFuncNum' => $cKEditorFuncNum,
            'linkChooser'     => $linkChooser,
            'linkChooserLink' => $linkChooserLink,
            'mediamanager'    => $mediaHandler,
            'handler'         => $handler,
            'type'            => $type,
            'folder'          => $folder,
            'folders'         => $folders,
            'adminlist'       => $adminList,
            'fileform'        => $this->createTypeFormView($mediaHandler, "file"),
            'videoform'       => $this->createTypeFormView($mediaHandler, "video"),
            'slideform'       => $this->createTypeFormView($mediaHandler, "slide"),
            'audioform'       => $this->createTypeFormView($mediaHandler, "audio")
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
