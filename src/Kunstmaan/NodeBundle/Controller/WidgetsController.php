<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WidgetsController extends AbstractController
{
    /** @var DomainConfigurationInterface */
    private $domainConfiguration;
    /** @var EntityManagerInterface */
    private $em;
    /** @var AclHelper */
    private $aclHelper;

    public function __construct(DomainConfigurationInterface $domainConfiguration, EntityManagerInterface $em, AclHelper $aclHelper)
    {
        $this->domainConfiguration = $domainConfiguration;
        $this->em = $em;
        $this->aclHelper = $aclHelper;
    }

    #[Route(path: '/ckselecturl', name: 'KunstmaanNodeBundle_ckselecturl')]
    public function ckSelectLinkAction(Request $request): Response
    {
        $params = $this->getTemplateParameters($request);
        $params['cke'] = true;
        $params['multilanguage'] = $this->getParameter('kunstmaan_admin.multi_language');

        return $this->render('@KunstmaanNode/Widgets/selectLink.html.twig', $params);
    }

    #[Route(path: '/selecturl', name: 'KunstmaanNodeBundle_selecturl')]
    public function selectLinkAction(Request $request): Response
    {
        $params = $this->getTemplateParameters($request);
        $params['cke'] = false;
        $params['multilanguage'] = $this->getParameter('kunstmaan_admin.multi_language');

        return $this->render('@KunstmaanNode/Widgets/selectLink.html.twig', $params);
    }

    #[Route(path: '/select-nodes-lazy', name: 'KunstmaanNodeBundle_nodes_lazy')]
    public function selectNodesLazy(Request $request): JsonResponse
    {
        $locale = $request->getLocale();
        $id = $request->query->get('id');
        $depth = $this->getParameter('kunstmaan_node.url_chooser.lazy_increment');

        if (!$id || $id === '#') {
            if ($this->domainConfiguration->isMultiDomainHost()) {
                $switchedHost = $this->domainConfiguration->getHostSwitched();
                $rootItems = [$this->domainConfiguration->getRootNode($switchedHost['host'])];
            } else {
                $rootItems = $this->em->getRepository(Node::class)->getAllTopNodes();
            }
        } else {
            $rootNode = $this->em->getRepository(Node::class)->find($id);
            $rootItems = $this->getChildren($locale, $rootNode);
        }

        $results = $this->nodesToArray($locale, $rootItems, $depth);

        return new JsonResponse($results);
    }

    /**
     * Search action in url chooser popup
     */
    #[Route(path: '/select-nodes-lazy_search', name: 'KunstmaanNodeBundle_nodes_lazy_search')]
    public function selectNodesLazySearch(Request $request): JsonResponse
    {
        $locale = $request->getLocale();
        $search = $request->query->get('str');

        $results = [];
        if ($search) {
            $nts = $this->em->getRepository(NodeTranslation::class)->getNodeTranslationsLikeTitle($search, $locale);
            foreach ($nts as $nt) {
                $node = $nt->getNode();
                $results[] = $node->getId();
                while ($node->getParent()) {
                    $node = $node->getParent();
                    $results[] = $node->getId();
                }
            }
            $results = array_unique($results);
            sort($results);
        }

        return new JsonResponse($results);
    }

    /**
     * Get the parameters needed in the template. This is common for the
     * default link chooser and the cke link chooser.
     */
    private function getTemplateParameters(Request $request): array
    {
        // When the media bundle is available, we show a link in the header to the media chooser
        $allBundles = $this->getParameter('kernel.bundles');
        $mediaChooserLink = null;

        if (\array_key_exists('KunstmaanMediaBundle', $allBundles)) {
            $params = ['linkChooser' => 1];
            $cKEditorFuncNum = $request->query->get('CKEditorFuncNum');
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
            }
            $mediaChooserLink = $this->generateUrl('KunstmaanMediaBundle_chooser', $params);
        }

        return [
            'mediaChooserLink' => $mediaChooserLink,
        ];
    }

    /**
     * Determine if current node is a structure node.
     *
     * @param string $refEntityName
     */
    protected function isStructureNode($refEntityName): bool
    {
        $structureNode = false;
        if (class_exists($refEntityName)) {
            $page = new $refEntityName();
            $structureNode = $page instanceof StructureNode;
            unset($page);
        }

        return $structureNode;
    }

    /**
     * Determine if current node is a structure node.
     *
     * @param string                 $locale
     * @param Node[]|ArrayCollection $rootNodes
     * @param int                    $depth
     */
    protected function nodesToArray($locale, $rootNodes, $depth = 2): array
    {
        $isMultiDomain = $this->domainConfiguration->isMultiDomainHost();
        $switchedHost = $this->domainConfiguration->getHostSwitched();
        $switched = null !== $switchedHost && array_key_exists('host', $switchedHost) && $this->domainConfiguration->getHost() === $switchedHost['host'];

        $results = [];

        /** @var Node $rootNode */
        foreach ($rootNodes as $rootNode) {
            if ($nodeTranslation = $rootNode->getNodeTranslation($locale, true)) {
                if ($isMultiDomain && !$switched) {
                    $slug = sprintf('[%s:%s]', $switchedHost['id'], 'NT' . $nodeTranslation->getId());
                } else {
                    $slug = sprintf('[%s]', 'NT' . $nodeTranslation->getId());
                }

                switch (true) {
                    case !$nodeTranslation->isOnline():
                        $type = 'offline';

                        break;
                    case $rootNode->isHiddenFromNav():
                        $type = 'hidden-from-nav';

                        break;
                    default:
                        $type = 'default';
                }

                $root = [
                    'id' => $rootNode->getId(),
                    'type' => $type,
                    'text' => $nodeTranslation->getTitle(),
                    'li_attr' => ['class' => 'js-url-chooser-link-select', 'data-slug' => $slug, 'data-id' => $rootNode->getId()],
                ];

                $children = $this->getChildren($locale, $rootNode);
                if ($children) {
                    if ($depth - 1) {
                        $root['children'] = $this->nodesToArray($locale, $children, --$depth);
                    } else {
                        $root['children'] = true;
                    }
                }
                $results[] = $root;
            }
        }

        return $results;
    }

    private function getChildren(string $locale, Node $rootNode)
    {
        $nodeRepository = $this->em->getRepository(Node::class);

        return $nodeRepository->getChildNodes(
            $rootNode->getId(),
            $locale,
            PermissionMap::PERMISSION_VIEW,
            $this->aclHelper,
            true,
            true,
            $rootNode
        );
    }
}
