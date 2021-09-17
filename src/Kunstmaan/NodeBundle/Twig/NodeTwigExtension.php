<?php

namespace Kunstmaan\NodeBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension to fetch node / translation by page in Twig templates
 *
 * @final since 5.4
 */
class NodeTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var NodeMenu
     */
    private $nodeMenu;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $generator,
        NodeMenu $nodeMenu,
        RequestStack $requestStack
    ) {
        $this->em = $em;
        $this->generator = $generator;
        $this->nodeMenu = $nodeMenu;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'get_node_for', [$this, 'getNodeFor']
            ),
            new TwigFunction(
                'get_node_translation_for',
                [$this, 'getNodeTranslationFor']
            ),
            new TwigFunction(
                'get_node_by_internal_name',
                [$this, 'getNodeByInternalName']
            ),
            new TwigFunction(
                'get_url_by_internal_name',
                [$this, 'getUrlByInternalName']
            ),
            new TwigFunction(
                'get_path_by_internal_name',
                [$this, 'getPathByInternalName']
            ),
            new TwigFunction(
                'get_page_by_node_translation',
                [$this, 'getPageByNodeTranslation']
            ),
            new TwigFunction(
                'get_node_menu',
                [$this, 'getNodeMenu']
            ),
            new TwigFunction(
                'is_structure_node',
                [$this, 'isStructureNode']
            ),
            new TwigFunction(
                'file_exists',
                [$this, 'fileExists']
            ),
            new TwigFunction(
                'get_node_trans_by_node_id',
                [$this, 'getNodeTranslationByNodeId']
            ),
            new TwigFunction(
                'getOverviewRoute',
                [$this, 'getOverviewRoute']
            ),
        ];
    }

    /**
     * Get the node translation object based on node id and language.
     *
     * @param int    $nodeId
     * @param string $lang
     *
     * @return NodeTranslation
     */
    public function getNodeTranslationByNodeId($nodeId, $lang)
    {
        $repo = $this->em->getRepository(NodeTranslation::class);

        return $repo->getNodeTranslationByNodeId($nodeId, $lang);
    }

    /**
     * @return object|null
     */
    public function getPageByNodeTranslation(NodeTranslation $nodeTranslation)
    {
        return $nodeTranslation->getRef($this->em);
    }

    /**
     * @return Node
     */
    public function getNodeFor(PageInterface $page)
    {
        return $this->em->getRepository(Node::class)->getNodeFor($page);
    }

    /**
     * @return NodeTranslation
     */
    public function getNodeTranslationFor(PageInterface $page)
    {
        return $this->em->getRepository(NodeTranslation::class)->getNodeTranslationFor($page);
    }

    /**
     * @param string $internalName
     * @param string $locale
     *
     * @return Node|null
     */
    public function getNodeByInternalName($internalName, $locale)
    {
        $nodes = $this->em->getRepository(Node::class)
            ->getNodesByInternalName($internalName, $locale);
        if (!empty($nodes)) {
            return $nodes[0];
        }

        return null;
    }

    /**
     * @param string $internalName Internal name of the node
     * @param string $locale       Locale
     * @param array  $parameters   (optional) extra parameters
     * @param bool   $relative     (optional) return relative path?
     *
     * @return string
     */
    public function getPathByInternalName($internalName, $locale, $parameters = [], $relative = false)
    {
        $routeParameters = $this->getRouteParametersByInternalName($internalName, $locale, $parameters);

        return $this->generator->generate(
            '_slug',
            $routeParameters,
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    /**
     * @param string $internalName   Internal name of the node
     * @param string $locale         Locale
     * @param array  $parameters     (optional) extra parameters
     * @param bool   $schemeRelative (optional) return relative scheme?
     *
     * @return string
     */
    public function getUrlByInternalName($internalName, $locale, $parameters = [], $schemeRelative = false)
    {
        $routeParameters = $this->getRouteParametersByInternalName($internalName, $locale, $parameters);

        return $this->generator->generate(
            '_slug',
            $routeParameters,
            $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $locale
     * @param Node   $node
     * @param bool   $includeHiddenFromNav
     *
     * @return NodeMenu
     */
    public function getNodeMenu($locale, Node $node = null, $includeHiddenFromNav = false)
    {
        $request = $this->requestStack->getMasterRequest();
        $isPreview = $request->attributes->has('preview') && $request->attributes->get('preview') === true;
        $this->nodeMenu->setLocale($locale);
        $this->nodeMenu->setCurrentNode($node);
        $this->nodeMenu->setIncludeOffline($isPreview);
        $this->nodeMenu->setIncludeHiddenFromNav($includeHiddenFromNav);

        return $this->nodeMenu;
    }

    public function isStructureNode($page)
    {
        return $page instanceof StructureNode;
    }

    public function fileExists($filename)
    {
        return file_exists($filename);
    }

    /**
     * @param string $internalName
     * @param string $locale
     * @param array  $parameters
     *
     * @return array
     */
    private function getRouteParametersByInternalName($internalName, $locale, $parameters = [])
    {
        $url = '';
        $translation = $this->em->getRepository(NodeTranslation::class)
            ->getNodeTranslationByLanguageAndInternalName($locale, $internalName);

        if (!\is_null($translation)) {
            $url = $translation->getUrl();
        }

        return array_merge(
            [
                'url' => $url,
                '_locale' => $locale,
            ],
            $parameters
        );
    }

    public function getOverviewRoute($node)
    {
        if ($node instanceof OverviewNavigationInterface) {
            return $node->getOverViewRoute();
        }

        return null;
    }
}
