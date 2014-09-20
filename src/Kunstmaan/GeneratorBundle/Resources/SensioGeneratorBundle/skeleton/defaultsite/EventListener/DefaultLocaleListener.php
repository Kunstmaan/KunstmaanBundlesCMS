<?php

namespace {{ namespace }}\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class DefaultLocaleListener
{
    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * If the response is a 404 and the URL is the root then redirect to the language root of the defaultlanguage.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // When we're on root and it's NOT succesful, redirect to the root for the defaultLocale.
        if (($this->isRootUrl($request)) && !$response->isSuccessful() && !$response->isRedirection()) {
            $response = new RedirectResponse($request->getBaseUrl() . '/' . $this->defaultLocale);
            $event->setResponse($response);
        }
    }

    private function isRootUrl(Request $request)
    {
        $url = $request->getPathInfo();
        return (empty($url) || ($url == '/'));
    }
}
