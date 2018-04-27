<?php

namespace {{ namespace }}\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
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
        return new RedirectResponse(
            $this->generateUrl(
                '_slug',
                [
                    'url'       => '',
                    '_locale'   => $this->getLocale($request)
                ]
            )
        );
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function getLocale(Request $request)
    {
        $locales = array_filter(
            explode('|', $this->container->getParameter('requiredlocales'))
        );
        return $request->getPreferredLanguage($locales);
    }
    {% endif %}

}
