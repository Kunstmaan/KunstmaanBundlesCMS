<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * ChooserController.
 */
class ChooserController extends Controller
{
    protected $em;


    /**
     * @Route("/chooser", name="KunstmaanMediaBundle_chooser")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function chooserIndexAction(Request $request)
    {
        $em       = $this->getDoctrine()->getManager();
        $session  = $request->getSession();
        $folderId = false;

        $type            = $request->get('type', 'all');
        $cKEditorFuncNum = $request->get('CKEditorFuncNum');
        $linkChooser     = $request->get('linkChooser');

        // Go to the last visited folder
        if ($session->get('last-media-folder')) {
            try {
                $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($session->get('last-media-folder'));
                $folderId = $session->get('last-media-folder');
            } catch (EntityNotFoundException $e) {
                $folderId = false;
            }
        }

        if (!$folderId) {
            // Redirect to the first top folder
            /* @var Folder $firstFolder */
            $firstFolder = $em->getRepository('KunstmaanMediaBundle:Folder')->getFirstTopFolder();
            $folderId    = $firstFolder->getId();
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
     * @param Request $request
     * @param int     $folderId The folder id
     *
     * @Route("/chooser/{folderId}", requirements={"folderId" = "\d+"}, name="KunstmaanMediaBundle_chooser_show_folder")
     * @Template()
     *
     * @return array
     */
    public function chooserShowFolderAction(Request $request, $folderId)
    {
        $this->em        = $this->getDoctrine()->getManager();
        $session         = $request->getSession();
        $type            = $request->get('type');
        $cKEditorFuncNum = $request->get('CKEditorFuncNum');
        $linkChooser     = $request->get('linkChooser');
        $linkChooserLink = $this->getLinkChooserLink($linkChooser, $cKEditorFuncNum);
        /* @var MediaManager $mediaManager */
        $mediaManager = $this->get('kunstmaan_media.media_manager');
        $session->set('last-media-folder', $folderId);
        $this->setViewMode($request, $session);
        /* @var Folder $folder */
        $folder = $this->em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($folderId);
        $handler = $this->getHandler($mediaManager, $type);
        $adminList = $this->getAdminList($request, $mediaManager, $folder);
        $subForm = $this->getSubForm($folder);

        $viewVars = ['cKEditorFuncNum' => $cKEditorFuncNum,
            'linkChooser' => $linkChooser,
            'linkChooserLink' => $linkChooserLink,
            'mediamanager' => $mediaManager,
            'foldermanager' => $this->get('kunstmaan_media.folder_manager'),
            'handler' => $handler,
            'type' => $type,
            'folder' => $folder,
            'adminlist' => $adminList,
            'subform' => $subForm->createView()
        ];

        $viewVars = $this->addFormsToVariables($mediaManager, $viewVars);
        return $viewVars;
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     */
    private function setViewMode(Request $request, SessionInterface $session)
    {
        // Check when user switches between thumb -and list view
        $viewMode = $request->query->get('viewMode');
        if ($viewMode && $viewMode == 'list-view') {
            $session->set('media-list-view', true);
        } elseif ($viewMode && $viewMode == 'thumb-view') {
            $session->remove('media-list-view');
        }
    }

    /**
     * @param $linkChooser
     * @param $cKEditorFuncNum
     * @return null|string
     */
    private function getLinkChooserLink($linkChooser, $cKEditorFuncNum)
    {
        $linkChooserLink = null;
        if (!empty($linkChooser)) {
            $params = [];
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
                $routeName                 = 'KunstmaanNodeBundle_ckselecturl';
            } else {
                $routeName = 'KunstmaanNodeBundle_selecturl';
            }
            $linkChooserLink = $this->generateUrl($routeName, $params);
        }
        return $linkChooserLink;
    }

    /**
     * @param MediaManager $mediaManager
     * @param string $type
     * @return AbstractMediaHandler|null
     */
    private function getHandler(MediaManager $mediaManager, $type)
    {
        /** @var AbstractMediaHandler $handler */
        $handler = null;
        if ($type) {
            $handler = $mediaManager->getHandlerForType($type);
        }
        return $handler;
    }

    /**
     * @param Request $request
     * @param MediaManager $mediaManager
     * @param $folder
     * @return AdminList
     */
    private function getAdminList(Request $request, MediaManager $mediaManager, $folder)
    {
        $adminListConfigurator = new MediaAdminListConfigurator($this->em, $mediaManager, $folder, $request);
        /** @var AdminList $adminList */
        $adminList             = $this->get('kunstmaan_adminlist.factory')->createList($adminListConfigurator);
        $adminList->bindRequest($request);
        return $adminList;
    }

    /**
     * @param $folder
     * @return \Symfony\Component\Form\Form
     */
    private function getSubForm($folder)
    {
        $sub = new Folder();
        $sub->setParent($folder);
        $subForm  = $this->createForm(FolderType::class, $sub, ['folder' => $sub]);
        return $subForm;
    }

    /**
     * @param MediaManager $mediaManager
     * @param array $viewVars
     * @return array
     */
    public function addFormsToVariables(MediaManager $mediaManager, array $viewVars)
    {
        $forms = [];

        foreach($mediaManager->getFolderAddActions()  as $addAction ) {
            $forms[$addAction['type']] = $this->createTypeFormView($mediaManager, $addAction['type']);
        }

        $viewVars['forms'] = $forms;
        return $viewVars;
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
        $media   = new Media();
        $helper  = $handler->getFormHelper($media);

        return $this->createForm($handler->getFormType(), $helper, $handler->getFormTypeOptions())->createView();
    }
}
