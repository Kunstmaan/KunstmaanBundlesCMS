<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * WidgetsController
 */
class WidgetsController extends Controller
{
    /**
     * @Route("/ckselecturl", name="KunstmaanNodeBundle_ckselecturl")
     * @Template("KunstmaanNodeBundle:Widgets:selectLink.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function ckSelectLinkAction(Request $request)
    {
        $params = $this->getTemplateParameters($request);
        $params['cke'] = true;
        $params['multilanguage'] = $this->getParameter('multilanguage');

        return $params;
    }

    /**
     * Select a link
     *
     * @Route("/selecturl", name="KunstmaanNodeBundle_selecturl")
     * @Template("KunstmaanNodeBundle:Widgets:selectLink.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function selectLinkAction(Request $request)
    {
        $params = $this->getTemplateParameters($request);
        $params['cke'] = false;
        $params['multilanguage'] = $this->getParameter('multilanguage');

        return $params;
    }

    /**
     * Select a link
     *
     * @Route("/select-nodes-lazy_search", name="KunstmaanNodeBundle_nodes_lazy_search")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     */
    public function selectNodesLazySearch(Request $request)
    {
        @trigger_error(sprintf('The "%s" controller action is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.', __METHOD__), E_USER_DEPRECATED);

        /* @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $search = $request->query->get('str');

        $results = [];
        if ($search) {
            $nts = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getNodeTranslationsLikeTitle($search, $locale);
            /** @var NodeTranslation $nt */
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
     * Select a link
     *
     * @Route("/select-nodes-lazy", name="KunstmaanNodeBundle_nodes_lazy")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     */
    public function selectNodesLazy(Request $request)
    {
        /* @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $id = $request->query->get('id');
        $depth = $this->getParameter('kunstmaan_node.url_chooser.lazy_increment');

        if (!$id || $id == '#') {
            $domainConfig = $this->get('kunstmaan_admin.domain_configuration');

            if ($domainConfig->isMultiDomainHost()) {
                $switchedHost = $domainConfig->getHostSwitched();
                $rootItems = [$domainConfig->getRootNode($switchedHost['host'])];
            } else {
                $rootItems = $em->getRepository('KunstmaanNodeBundle:Node')->getAllTopNodes();
            }
        } else {
            $rootItems = $em->getRepository('KunstmaanNodeBundle:Node')->find($id)->getChildren();
        }

        $results = $this->nodesToArray($locale, $rootItems, $depth);

        return new JsonResponse($results);
    }

    /**
     * Get the parameters needed in the template. This is common for the
     * default link chooser and the cke link chooser.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    private function getTemplateParameters(Request $request)
    {
        // When the media bundle is available, we show a link in the header to the media chooser
        $allBundles = $this->getParameter('kernel.bundles');
        $mediaChooserLink = null;

        if (array_key_exists('KunstmaanMediaBundle', $allBundles)) {
            $params = ['linkChooser' => 1];
            $cKEditorFuncNum = $request->get('CKEditorFuncNum');
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
            }
            $mediaChooserLink = $this->generateUrl(
                'KunstmaanMediaBundle_chooser',
                $params
            );
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
        /** @var DomainConfiguration $domainconfig */
        $domainconfig = $this->get('kunstmaan_admin.domain_configuration');
        $isMultiDomain = $domainconfig->isMultiDomainHost();
        $switchedHost = $domainconfig->getHostSwitched();
        $switched = $domainconfig->getHost() == $switchedHost['host'];

        $results = [];

        /** @var Node $rootNode */
        foreach ($rootNodes as $rootNode) {
            if ($nodeTranslation = $rootNode->getNodeTranslation($locale, true)) {
                if ($isMultiDomain && !$switched) {
                    $slug = sprintf('[%s:%s]', $switchedHost['id'], 'NT' . $nodeTranslation->getId());
                } else {
                    $slug = sprintf('[%s]', 'NT' . $nodeTranslation->getId());
                }

                $root = [
                    'id' => $rootNode->getId(),
                    'type' => $nodeTranslation->isOnline() ? 'default' : 'offline',
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
