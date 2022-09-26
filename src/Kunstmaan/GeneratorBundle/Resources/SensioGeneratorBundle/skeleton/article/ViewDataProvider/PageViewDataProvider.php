<?php

declare(strict_types=1);

namespace {{ namespace }}\ViewDataProvider;

use App\Entity\Pages\{{ entity_class }}Page;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class {{ entity_class }}PageViewDataProvider implements PageViewDataProviderInterface
{
    /** @var RequestStack */
    private $requestStack;
    /** @var EntityManagerInterface */
    private $em;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
    }

    public function provideViewData(NodeTranslation $nodeTranslation, RenderContext $renderContext): void
    {
        {% if useMainRequest %}
$request = $this->requestStack->getMainRequest();
        {% else %}
$request = $this->requestStack->getMasterRequest();
        {% endif %}

        if (null === $request) {
            return;
        }

        $searchCategory = $request->query->get('category') ? explode(',', $request->query->get('category')) : null;
        $searchTag = $request->query->get('tag') ? explode(',', $request->query->get('tag')) : null;

        $pageRepository = $this->em->getRepository({{ entity_class }}Page::class);
        $result = $pageRepository->getArticles($request->getLocale(), null, null, $searchCategory, $searchTag);

        // Filter the results for this page
        $adapter = new ArrayAdapter($result);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagenumber = $request->query->getInt('page', 1);
        $pagenumber = $pagenumber < 1 ? 1 : $pagenumber;

        try {
            $pagerfanta->setCurrentPage($pagenumber);
        } catch (OutOfRangeCurrentPageException $oore) {
            $repo = $this->em->getRepository(NodeTranslation::class);
            $nt = $repo->getNodeTranslationByLanguageAndInternalName($request->getLocale(), '{{ entity_class|lower }}');
            $url = $this->urlGenerator->generate('_slug', ['url' => $nt ? $nt->getUrl() : '', '_locale' => $request->getLocale()]);

            $renderContext->setResponse(new RedirectResponse($url));

            return;
        }

        $results = $pagerfanta->getCurrentPageResults();

        $renderContext['results'] = $results;
        $renderContext['pagerfanta'] = $pagerfanta;
        $renderContext['nodeTranslation'] = $nodeTranslation;
    }
}
