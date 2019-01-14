<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;

/**
 * PagePartTwigExtension
 */
class PagePartTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_pageparts', array($this, 'renderPageParts'), array('needs_environment' => true, 'needs_context' => true, 'is_safe' => array('html'))),
            new \Twig_SimpleFunction('getpageparts', array('needs_environment' => true, $this, 'getPageParts')),
            new \Twig_SimpleFunction('has_page_parts', [$this, 'hasPageParts']),
        );
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
    public function renderPageParts(\Twig_Environment $env, array $twigContext, HasPagePartsInterface $page, $contextName = 'main', array $parameters = array())
    {
        $template = $env->loadTemplate('KunstmaanPagePartBundle:PagePartTwigExtension:widget.html.twig');
        /* @var $entityRepository PagePartRefRepository */
        $pageparts = $this->getPageParts($page, $contextName);
        $newTwigContext = array_merge($parameters, array(
            'pageparts' => $pageparts,
            'page' => $page,
        ));
        $newTwigContext = array_merge($newTwigContext, $twigContext);

        return $template->render($newTwigContext);
    }

    /**
     * @param HasPagePartsInterface $page    The page
     * @param string                $context The pagepart context
     *
     * @return PagePartInterface[]
     */
    public function getPageParts(HasPagePartsInterface $page, $context = 'main')
    {
        /** @var $entityRepository PagePartRefRepository */
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pageparts = $entityRepository->getPageParts($page, $context);

        return $pageparts;
    }

    /**
     * @param HasPagePartsInterface $page
     * @param string                $context
     *
     * @return bool
     */
    public function hasPageParts(HasPagePartsInterface $page, $context = 'main')
    {
        return $this->em->getRepository(PagePartRef::class)->hasPageParts($page, $context);
    }
}
