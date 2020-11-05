<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * PagePartTwigExtension
 *
 * @final since 5.4
 */
class PagePartTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('render_pageparts', [$this, 'renderPageParts'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('getpageparts', ['needs_environment' => true, $this, 'getPageParts']),
            new TwigFunction('has_page_parts', [$this, 'hasPageParts']),
        ];
    }

    /**
     * @param array                 $twigContext The twig context
     * @param HasPagePartsInterface $page        The page
     * @param string                $contextName The pagepart context
     * @param array                 $parameters  Some extra parameters
     *
     * @return string
     */
    public function renderPageParts(Environment $env, array $twigContext, HasPagePartsInterface $page, $contextName = 'main', array $parameters = [])
    {
        $template = $env->load('@KunstmaanPagePart/PagePartTwigExtension/widget.html.twig');
        /* @var $entityRepository PagePartRefRepository */
        $pageparts = $this->getPageParts($page, $contextName);
        $newTwigContext = array_merge($parameters, [
            'pageparts' => $pageparts,
            'page' => $page,
        ]);
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
        $entityRepository = $this->em->getRepository(PagePartRef::class);

        return $entityRepository->getPageParts($page, $context);
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    public function hasPageParts(HasPagePartsInterface $page, $context = 'main')
    {
        return $this->em->getRepository(PagePartRef::class)->hasPageParts($page, $context);
    }
}
