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
 */
final class NodeTwigExtension extends AbstractExtension
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
        RequestStack $requestStack,
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
    public function getFunctions(): array
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
            new TwigFunction('get_node_translation_by_internal_name', [$this, 'getNodeTranslationByInternalName']),
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
     */
    public function getNodeTranslationByNodeId($nodeId, $lang): ?NodeTranslation
    {
        return $this->em->getRepository(NodeTranslation::class)->getNodeTranslationByNodeId($nodeId, $lang);
    }

    public function getPageByNodeTranslation(NodeTranslation $nodeTranslation): ?object
    {
        return $nodeTranslation->getRef($this->em);
    }

    public function getNodeFor(PageInterface $page): Node
    {
        return $this->em->getRepository(Node::class)->getNodeFor($page);
    }

    public function getNodeTranslationFor(PageInterface $page): ?NodeTranslation
    {
        return $this->em->getRepository(NodeTranslation::class)->getNodeTranslationFor($page);
    }

    /**
     * @param string $internalName
     * @param string $locale
     */
    public function getNodeByInternalName($internalName, $locale): ?Node
    {
        $nodes = $this->em->getRepository(Node::class)
            ->getNodesByInternalName($internalName, $locale);
        if (!empty($nodes)) {
            return $nodes[0];
        }

        return null;
    }

    public function getNodeTranslationByInternalName(string $internalName, string $locale): ?NodeTranslation
    {
        return $this->em->getRepository(NodeTranslation::class)->getNodeTranslationByLanguageAndInternalName($locale, $internalName);
    }

    /**
     * @param string $internalName Internal name of the node
     * @param string $locale       Locale
     * @param array  $parameters   (optional) extra parameters
     * @param bool   $relative     (optional) return relative path?
     */
    public function getPathByInternalName($internalName, $locale, $parameters = [], $relative = false): string
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
     */
    public function getUrlByInternalName($internalName, $locale, $parameters = [], $schemeRelative = false): string
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
     * @param bool   $includeHiddenFromNav
     */
    public function getNodeMenu($locale, ?Node $node = null, $includeHiddenFromNav = false): NodeMenu
    {
        $request = $this->requestStack->getMainRequest();
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
     */
    private function getRouteParametersByInternalName($internalName, $locale, $parameters = []): array
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
