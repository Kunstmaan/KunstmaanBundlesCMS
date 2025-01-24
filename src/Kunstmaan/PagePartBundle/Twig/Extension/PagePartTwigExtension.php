<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PagePartTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
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
     */
    public function renderPageParts(Environment $env, array $twigContext, HasPagePartsInterface $page, $contextName = 'main', array $parameters = []): string
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
    public function getPageParts(HasPagePartsInterface $page, $context = 'main'): array
    {
        /** @var $entityRepository PagePartRefRepository */
        $entityRepository = $this->em->getRepository(PagePartRef::class);

        return $entityRepository->getPageParts($page, $context);
    }

    /**
     * @param string $context
     */
    public function hasPageParts(HasPagePartsInterface $page, $context = 'main'): bool
    {
        return $this->em->getRepository(PagePartRef::class)->hasPageParts($page, $context);
    }
}
