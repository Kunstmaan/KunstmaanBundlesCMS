<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\NodeHelper;
use Kunstmaan\NodeBundle\Helper\Rendering\NodeRenderingInterface;
use Kunstmaan\NodeBundle\Helper\Services\NodeVersionAutoSaveInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AutoSaveController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

    /** @var NodeHelper */
    private $nodeHelper;

    /** @var NodeVersionAutoSaveInterface */
    private $autoSaver;

    /** @var NodeRenderingInterface */
    private $nodeRenderer;

    public function __construct(
        EntityManager $em,
        NodeHelper $nodeHelper,
        NodeVersionAutoSaveInterface $autoSaver,
        NodeRenderingInterface $nodeRenderer
    ) {
        $this->em = $em;
        $this->nodeHelper = $nodeHelper;
        $this->autoSaver = $autoSaver;
        $this->nodeRenderer = $nodeRenderer;
    }

    /**
     * @Route(
     *      "/{id}/auto-save",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_auto_save_node_verison",
     *      methods={"POST"}
     * )
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function autoSaveAction(Request $request, int $id)
    {
        $locale = $request->getLocale();
        /* @var HasNodeInterface $page */
        $page = null;
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);
        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        if (!$nodeTranslation) {
            return new NotFoundHttpException();
        }
        $publicVersion = $nodeTranslation->getPublicNodeVersion();
        if (!$publicVersion) {
            return new NotFoundHttpException();
        }
        $page = $publicVersion->getRef($this->em);
        $isStructureNode = $page->isStructureNode();
        /* no need to autosave structurenodes */
        if ($isStructureNode) {
            return new Response();
        }

        $nodeVersion = $this->nodeHelper->createAutoSaveVersion(
            $page,
            $nodeTranslation,
            $publicVersion
        );
        $page = $nodeVersion->getRef($this->em);
        $validAutoSave = $this->autoSaver->updateAutoSaveFromInput($page, $request, $node, $nodeTranslation, $isStructureNode, $nodeVersion);
        if ($validAutoSave) {
            return new Response();
        }

        return $this->nodeRenderer->render($locale, $node, $nodeTranslation, $page, $request);
    }
}
