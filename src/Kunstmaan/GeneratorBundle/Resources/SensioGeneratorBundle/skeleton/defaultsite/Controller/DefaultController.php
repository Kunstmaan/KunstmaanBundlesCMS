<?php

namespace {{ namespace }}\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    {% if multilanguage %}
    /**
     * @Route("/")
     * @param Request $request
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('_slug', array_merge($request->query->all(), [
            'url' => '',
            '_locale' => $this->getLocale($request),
        ]));
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function getLocale(Request $request)
    {
        $locales = array_filter(
            explode('|', $this->getParameter('requiredlocales'))
        );
        return $request->getPreferredLanguage($locales);
    }
    {% endif %}

}
