<?php
namespace {{ namespace }}\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;

class {{ entity_class }}ArticleController extends AbstractController
{
    public function serviceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $searchCategory = $request->get('category') ? explode(',', $request->get('category')) : null;
        $searchTag = $request->get('tag') ? explode(',', $request->get('tag')) : null;

        $pageRepository = $em->getRepository('{{ bundle.getName() }}:Pages\{{ entity_class }}Page');
        $result = $pageRepository->getArticles($request->getLocale(), null, null, $searchCategory, $searchTag);

        // Filter the results for this page
        $adapter = new ArrayAdapter($result);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagenumber = (int) $request->get('page');
        if (!$pagenumber || $pagenumber < 1) {
            $pagenumber = 1;
        }
        try {
            $pagerfanta->setCurrentPage($pagenumber);
        } catch (OutOfRangeCurrentPageException $oore) {
            $repo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');
            $nt = $repo->getNodeTranslationByLanguageAndInternalName($request->getLocale(), '{{ entity_class|lower }}');
            $url = $nt ? $nt->getUrl() : '';
            $url = $this->generateUrl('_slug', array('url' => $url, '_locale' => $request->getLocale()));

            return new RedirectResponse($url);
        }

        // Add {{ entity_class|lower }} post url's
        $results = $pagerfanta->getCurrentPageResults();

        $context['results'] = $results;
        $context['pagerfanta'] = $pagerfanta;
        $context['nodeTranslation'] = $request->attributes->get('_nodeTranslation');
        $request->attributes->set('_renderContext', $context);
    }
}
