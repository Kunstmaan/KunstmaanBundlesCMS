<?php

declare(strict_types=1);

namespace Kunstmaan\NodeSearchBundle\ViewDataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\Services\SearchService;
use Symfony\Component\HttpFoundation\RequestStack;

final class SearchPageViewDataProvider implements PageViewDataProviderInterface
{
    public const MAX_QUERY_INPUT_LENGTH = 100;

    /** @var RequestStack */
    private $requestStack;
    /** @var SearchService */
    private $searchService;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(RequestStack $requestStack, SearchService $searchService, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->searchService = $searchService;
        $this->em = $em;
    }

    public function provideViewData(NodeTranslation $nodeTranslation, RenderContext $renderContext): void
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        if (!$request->query->has('query')) {
            return;
        }
        $request->query->set('query', substr($request->query->get('query'), 0, self::MAX_QUERY_INPUT_LENGTH));

        // The search service needs the page entity to be set on the request
        $request->attributes->set('_entity', $nodeTranslation->getRef($this->em));

        $pagerfanta = $this->searchService->search();
        $searchRenderContext = $this->searchService->getRenderContext();
        foreach ($searchRenderContext->getArrayCopy() as $key => $value) {
            $renderContext[$key] = $value;
        }
        $renderContext['pagerfanta'] = $pagerfanta;
    }
}
