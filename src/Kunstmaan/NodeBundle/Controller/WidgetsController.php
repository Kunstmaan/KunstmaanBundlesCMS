<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Entity\Node;
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

    public function __construct(DomainConfigurationInterface $domainConfiguration, EntityManagerInterface $em)
    {
        $this->domainConfiguration = $domainConfiguration;
        $this->em = $em;
    }

    /**
     * @Route("/ckselecturl", name="KunstmaanNodeBundle_ckselecturl")
     */
    public function ckSelectLinkAction(Request $request): Response
    {
        $params = $this->getTemplateParameters($request);
        $params['cke'] = true;
        $params['multilanguage'] = $this->getParameter('kunstmaan_admin.multi_language');

        return $this->render('@KunstmaanNode/Widgets/selectLink.html.twig', $params);
    }

    /**
     * Select a link
     *
     * @Route("/selecturl", name="KunstmaanNodeBundle_selecturl")
     */
    public function selectLinkAction(Request $request): Response
    {
        $params = $this->getTemplateParameters($request);
        $params['cke'] = false;
        $params['multilanguage'] = $this->getParameter('kunstmaan_admin.multi_language');

        return $this->render('@KunstmaanNode/Widgets/selectLink.html.twig', $params);
    }

    /**
     * Select a link
     *
     * @Route("/select-nodes-lazy", name="KunstmaanNodeBundle_nodes_lazy")
     */
    public function selectNodesLazy(Request $request): JsonResponse
    {
        $locale = $request->getLocale();
        $id = $request->query->get('id');
        $depth = $this->getParameter('kunstmaan_node.url_chooser.lazy_increment');

        if (!$id || $id == '#') {
            if ($this->domainConfiguration->isMultiDomainHost()) {
                $switchedHost = $this->domainConfiguration->getHostSwitched();
                $rootItems = [$this->domainConfiguration->getRootNode($switchedHost['host'])];
            } else {
                $rootItems = $this->em->getRepository(Node::class)->getAllTopNodes();
            }
        } else {
            $rootItems = $this->em->getRepository(Node::class)->find($id)->getChildren();
        }

        $results = $this->nodesToArray($locale, $rootItems, $depth);

        return new JsonResponse($results);
    }

    /**
     * Get the parameters needed in the template. This is common for the
     * default link chooser and the cke link chooser.
     *
     * @return array
     */
    private function getTemplateParameters(Request $request)
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
     *
     * @return bool
     */
    protected function isStructureNode($refEntityName)
    {
        $structureNode = false;
        if (class_exists($refEntityName)) {
            $page = new $refEntityName();
            $structureNode = ($page instanceof StructureNode);
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
     *
     * @return array
     */
    protected function nodesToArray($locale, $rootNodes, $depth = 2)
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

                if ($rootNode->getChildren()->count()) {
                    if ($depth - 1) {
                        $root['children'] = $this->nodesToArray($locale, $rootNode->getChildren(), --$depth);
                    } else {
                        $root['children'] = true;
                    }
                }
                $results[] = $root;
            }
        }

        return $results;
    }
}
