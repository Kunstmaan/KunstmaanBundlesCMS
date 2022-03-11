<?php

declare(strict_types=1);

namespace Kunstmaan\ArticleBundle\ViewDataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

final class ArticlePageViewDataProvider implements PageViewDataProviderInterface
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RequestStack */
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function provideViewData(NodeTranslation $nodeTranslation, RenderContext $renderContext): void
    {
        $request = method_exists($this->requestStack, 'getMainRequest') ? $this->requestStack->getMainRequest() : $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        $entity = $nodeTranslation->getRef($this->em);
        $repository = $entity->getArticleRepository($this->em);
        $pages = $repository->getArticles($request->getLocale());

        $adapter = new ArrayAdapter($pages);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);

        $pagenumber = $request->get('page');
        if (!$pagenumber || $pagenumber < 1) {
            $pagenumber = 1;
        }

        $pagerfanta->setCurrentPage($pagenumber);
        $renderContext['pagerfanta'] = $pagerfanta;
    }
}
