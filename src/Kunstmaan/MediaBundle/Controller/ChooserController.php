<?php

namespace Kunstmaan\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\MediaBundle\AdminList\MediaAdminListConfigurator;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Form\FolderType;
use Kunstmaan\MediaBundle\Helper\FolderManager;
use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChooserController extends AbstractController
{
    /** @var MediaManager */
    private $mediaManager;
    /** @var FolderManager */
    private $folderManager;
    /** @var AdminListFactory */
    private $adminListFactory;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(MediaManager $mediaManager, FolderManager $folderManager, AdminListFactory $adminListFactory, EntityManagerInterface $em)
    {
        $this->mediaManager = $mediaManager;
        $this->folderManager = $folderManager;
        $this->adminListFactory = $adminListFactory;
        $this->em = $em;
    }

    private const TYPE_ALL = 'all';

    #[Route(path: '/chooser', name: 'KunstmaanMediaBundle_chooser')]
    public function chooserIndexAction(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $folderId = false;

        $type = $request->query->get('type', self::TYPE_ALL);
        $cKEditorFuncNum = $request->query->get('CKEditorFuncNum');
        $linkChooser = $request->query->get('linkChooser');

        // Go to the last visited folder
        if ($session->get('last-media-folder')) {
            try {
                $this->em->getRepository(Folder::class)->getFolder($session->get('last-media-folder'));
                $folderId = $session->get('last-media-folder');
            } catch (EntityNotFoundException $e) {
                $folderId = false;
            }
        }

        if (!$folderId) {
            // Redirect to the first top folder
            /* @var Folder $firstFolder */
            $firstFolder = $this->em->getRepository(Folder::class)->getFirstTopFolder();
            $folderId = $firstFolder->getId();
        }

        $params = [
            'folderId' => $folderId,
            'type' => $type,
            'CKEditorFuncNum' => $cKEditorFuncNum,
            'linkChooser' => $linkChooser,
        ];

        return $this->redirectToRoute('KunstmaanMediaBundle_chooser_show_folder', $params);
    }

    /**
     * @param int $folderId The folder id
     */
    #[Route(path: '/chooser/{folderId}', requirements: ['folderId' => '\d+'], name: 'KunstmaanMediaBundle_chooser_show_folder')]
    public function chooserShowFolderAction(Request $request, $folderId, array $customViewVars = []): Response
    {
        $session = $request->getSession();

        $type = $request->query->get('type');
        $cKEditorFuncNum = $request->query->get('CKEditorFuncNum');
        $linkChooser = $request->query->get('linkChooser');

        // Remember the last visited folder in the session
        $session->set('last-media-folder', $folderId);

        // Check when user switches between thumb -and list view
        $viewMode = $request->query->get('viewMode');
        if ($viewMode && $viewMode == 'list-view') {
            $session->set('media-list-view', true);
        } elseif ($viewMode && $viewMode == 'thumb-view') {
            $session->remove('media-list-view');
        }

        /* @var Folder $folder */
        $folder = $this->em->getRepository(Folder::class)->getFolder($folderId);

        /** @var AbstractMediaHandler $handler */
        $handler = null;
        if ($type && $type !== self::TYPE_ALL) {
            $handler = $this->mediaManager->getHandlerForType($type);
        }

        $adminListConfigurator = new MediaAdminListConfigurator($this->em, $this->mediaManager, $folder, $request);
        $adminList = $this->adminListFactory->createList($adminListConfigurator);
        $adminList->bindRequest($request);

        $sub = new Folder();
        $sub->setParent($folder);
        $subForm = $this->createForm(FolderType::class, $sub, ['folder' => $sub]);

        $linkChooserLink = null;
        if (!empty($linkChooser)) {
            $params = [];
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
                $routeName = 'KunstmaanNodeBundle_ckselecturl';
            } else {
                $routeName = 'KunstmaanNodeBundle_selecturl';
            }
            $linkChooserLink = $this->generateUrl($routeName, $params);
        }

        $viewVars = [
            'cKEditorFuncNum' => $cKEditorFuncNum,
            'linkChooser' => $linkChooser,
            'linkChooserLink' => $linkChooserLink,
            'mediamanager' => $this->mediaManager,
            'foldermanager' => $this->folderManager,
            'handler' => $handler,
            'type' => $type,
            'folder' => $folder,
            'adminlist' => $adminList,
            'subform' => $subForm->createView(),
            ...$customViewVars,
        ];

        $forms = [];
        foreach ($this->mediaManager->getFolderAddActions() as $addAction) {
            $forms[$addAction['type']] = $this->createTypeFormView($this->mediaManager, $addAction['type']);
        }

        $viewVars['forms'] = $forms;

        return $this->render('@KunstmaanMedia/Chooser/chooserShowFolder.html.twig', $viewVars);
    }

    /**
     * @param string $type
     *
     * @return \Symfony\Component\Form\FormView
     */
    private function createTypeFormView(MediaManager $mediaManager, $type)
    {
        $handler = $mediaManager->getHandlerForType($type);
        $media = new Media();
        $helper = $handler->getFormHelper($media);

        return $this->createForm($handler->getFormType(), $helper, $handler->getFormTypeOptions())->createView();
    }
}
