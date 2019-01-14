<?php

namespace Kunstmaan\NodeSearchBundle\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

class KunstmaanNodeSearchTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var IndexablePagePartsService
     */
    private $indexablePagePartsService;

    /**
     * @param EntityManager             $em
     * @param IndexablePagePartsService $indexablePagePartsService
     */
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
        return array(
            new \Twig_SimpleFunction('get_parent_page', array($this, 'getParentPage')),
            new \Twig_SimpleFunction('render_indexable_pageparts', array($this, 'renderIndexablePageParts'), array('needs_environment' => true, 'needs_context' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * @param HasNodeInterface $page
     * @param string           $locale
     *
     * @return HasNodeInterface
     */
    public function getParentPage(HasNodeInterface $page, $locale)
    {
        /** @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($page);
        $parentNode = $node->getParent();
        $nodeTranslation = $parentNode->getNodeTranslation($locale);
        $parentPage = $nodeTranslation->getRef($this->em);

        return $parentPage;
    }

    /**
     * @param \Twig_Environment     $env
     * @param array                 $twigContext The twig context
     * @param HasPagePartsInterface $page        The page
     * @param string                $contextName The pagepart context
     * @param array                 $parameters  Some extra parameters
     *
     * @return string
     */
    public function renderIndexablePageParts(
        \Twig_Environment $env,
        array $twigContext,
        HasPagePartsInterface $page,
        $contextName = 'main',
        array $parameters = array()
    ) {
        $template = $env->loadTemplate('KunstmaanNodeSearchBundle:PagePart:view.html.twig');
        $pageparts = $this->indexablePagePartsService->getIndexablePageParts($page, $contextName);
        $newTwigContext = array_merge(
            $parameters,
            array(
                'pageparts' => $pageparts,
            )
        );
        $newTwigContext = array_merge($newTwigContext, $twigContext);

        return $template->render($newTwigContext);
    }
}
