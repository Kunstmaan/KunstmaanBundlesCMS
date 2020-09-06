<?php

namespace {{ namespace }}\EventSubscriber;

use {{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage;
use {{ namespace }}\Entity\Pages\{{ entity_class }}Page;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\PageRenderEvent;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class {{ entity_class }}ArticleEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PAGE_RENDER => 'customizePageRender',
        ];
    }

    public function customizePageRender(PageRenderEvent $event)
    {
        if (!$event->getPage() instanceof {{ entity_class }}OverviewPage) {
            return;
        }

        $request = $event->getRequest();

        $searchCategory = $request->get('category') ? explode(',', $request->get('category')) : null;
        $searchTag = $request->get('tag') ? explode(',', $request->get('tag')) : null;

        $pageRepository = $this->em->getRepository({{ entity_class }}Page::class);
        $result = $pageRepository->getArticles($request->getLocale(), null, null, $searchCategory, $searchTag);

        // Filter the results for this page
        $pagerfanta = new Pagerfanta(new ArrayAdapter($result));
        $pagerfanta->setMaxPerPage(10);

        $pagenumber = (int) $request->get('page');
        if (!$pagenumber || $pagenumber < 1) {
            $pagenumber = 1;
        }

        try {
            $pagerfanta->setCurrentPage($pagenumber);
        } catch (OutOfRangeCurrentPageException $e) {
            $repo = $this->em->getRepository(NodeTranslation::class);
            $nt = $repo->getNodeTranslationByLanguageAndInternalName($request->getLocale(), '{{ entity_class|lower }}_overview_page'); //TODO: check if the page internal name is correct
            $url = $this->urlGenerator->generate('_slug', array('url' => $nt ? $nt->getUrl() : '', '_locale' => $request->getLocale()));

            $event->setResponse(new RedirectResponse($url));

            return;
        }

        $renderContext = $event->getRenderContext();

        $renderContext['results'] = $pagerfanta->getCurrentPageResults();
        $renderContext['pagerfanta'] = $pagerfanta;
        $renderContext['nodeTranslation'] = $request->attributes->get('_nodeTranslation');
    }
}
