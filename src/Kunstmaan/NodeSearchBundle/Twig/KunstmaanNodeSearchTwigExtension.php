<?php

namespace Kunstmaan\NodeSearchBundle\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class KunstmaanNodeSearchTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var IndexablePagePartsService
     */
    private $indexablePagePartsService;

    public function __construct(EntityManager $em, IndexablePagePartsService $indexablePagePartsService)
    {
        $this->em = $em;
        $this->indexablePagePartsService = $indexablePagePartsService;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_parent_page', [$this, 'getParentPage']),
            new TwigFunction('render_indexable_pageparts', [$this, 'renderIndexablePageParts'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @return HasNodeInterface
     */
    public function getParentPage(HasNodeInterface $page, string $locale, bool $includeOffline = false)
    {
        /** @var Node $node */
        $node = $this->em->getRepository(Node::class)->getNodeFor($page);
        $parentNode = $node->getParent();
        $nodeTranslation = $parentNode->getNodeTranslation($locale, $includeOffline);

        return $nodeTranslation->getRef($this->em);
    }

    /**
     * @param array                 $twigContext The twig context
     * @param HasPagePartsInterface $page        The page
     * @param string                $contextName The pagepart context
     * @param array                 $parameters  Some extra parameters
     *
     * @return string
     */
    public function renderIndexablePageParts(
        Environment $env,
        array $twigContext,
        HasPagePartsInterface $page,
        $contextName = 'main',
        array $parameters = []
    ) {
        $template = $env->load('@KunstmaanNodeSearch/PagePart/view.html.twig');
        $pageparts = $this->indexablePagePartsService->getIndexablePageParts($page, $contextName);
        $newTwigContext = array_merge(
            $parameters,
            [
                'pageparts' => $pageparts,
            ]
        );
        $newTwigContext = array_merge($newTwigContext, $twigContext);

        return $template->render($newTwigContext);
    }
}
