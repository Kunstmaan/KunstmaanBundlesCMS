<?php

namespace {{ namespace }}\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
{% if multilanguage %}
{% if canUseAttributes %}
    #[Route('/')]
{% else %}/**
     * @Route("/")
     */
{% endif %}
    public function indexAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('_slug', array_merge($request->query->all(), [
            'url' => '',
            '_locale' => $this->getLocale($request),
        ]));
    }

    private function getLocale(Request $request): ?string
    {
        $locales = array_filter(
            explode('|', $this->getParameter('requiredlocales'))
        );

        return $request->getPreferredLanguage($locales);
    }
{% endif %}
}
