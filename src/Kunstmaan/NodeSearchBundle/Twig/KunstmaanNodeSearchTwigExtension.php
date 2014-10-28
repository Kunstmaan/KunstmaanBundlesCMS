<?php

namespace Kunstmaan\NodeSearchBundle\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
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
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param EntityManager             $em
     * @param IndexablePagePartsService $indexablePagePartsService
     */
    public function __construct(EntityManager $em, IndexablePagePartsService $indexablePagePartsService)
    {
        $this->em                        = $em;
        $this->indexablePagePartsService = $indexablePagePartsService;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
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
            'render_indexable_pageparts' => new \Twig_Function_Method(
                $this, 'renderIndexablePageParts', array(
                    'needs_context' => true,
                    'is_safe'       => array('html')
                )
            ),
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
        $node            = $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($page);
        $parentNode      = $node->getParent();
        $nodeTranslation = $parentNode->getNodeTranslation($locale);
        $parentPage      = $nodeTranslation->getRef($this->em);

        return $parentPage;
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
        array $twigContext,
        HasPagePartsInterface $page,
        $contextName = 'main',
        array $parameters = array()
    ) {
        $template       = $this->environment->loadTemplate('KunstmaanNodeSearchBundle:PagePart:view.html.twig');
        $pageparts      = $this->indexablePagePartsService->getIndexablePageParts($page, $contextName);
        $newTwigContext = array_merge(
            $parameters,
            array(
                'pageparts' => $pageparts
            )
        );
        $newTwigContext = array_merge($newTwigContext, $twigContext);

        return $template->render($newTwigContext);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_node_search_twig_extension';
    }
}
